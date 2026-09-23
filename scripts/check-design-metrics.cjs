const { chromium, firefox, webkit } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const fs = require('fs');
const path = require('path');

const output = process.env.OC_AUDIT_OUTPUT;
if (!output) throw new Error('Set OC_AUDIT_OUTPUT to an evidence directory');
fs.mkdirSync(output, { recursive: true });

const expectedSpacing = { 10: 4, 20: 8, 30: 16, 40: 24, 50: 40, 60: 64, 70: 96 };
const viewports = [1440, 768, 390, 320];
const urls = (process.env.OC_METRIC_URLS || 'http://localhost:8090/,http://localhost:8090/?page_id=5').split(',');
const requested = (process.env.OC_BROWSERS || 'edge,firefox,webkit').split(',');

const engines = {
  edge: () => chromium.launch({ headless: true, executablePath: process.env.BROWSER_EXE }),
  firefox: () => firefox.launch({ headless: true }),
  webkit: () => webkit.launch({ headless: true }),
};

(async () => {
  const results = [];
  const failures = [];
  for (const name of requested) {
    if (!engines[name]) throw new Error(`Unknown browser: ${name}`);
    const browser = await engines[name]();
    try {
      const page = await browser.newPage();
      for (const width of viewports) {
        await page.setViewportSize({ width, height: 1000 });
        for (const url of urls) {
          const response = await page.goto(url, { waitUntil: 'load', timeout: 60000 });
          const isJapanese = url.includes('oc_japanese_refined=1');
          const metrics = await page.evaluate(({ expectedSpacing, expectedBodyRatio }) => {
            const number = value => Number.parseFloat(value || '0');
            const ratio = element => {
              const style = getComputedStyle(element);
              return number(style.lineHeight) / number(style.fontSize);
            };
            const root = getComputedStyle(document.documentElement);
            const probe = document.createElement('div');
            probe.style.cssText = 'position:absolute;visibility:hidden;pointer-events:none';
            document.body.appendChild(probe);
            const spacing = Object.fromEntries(Object.keys(expectedSpacing).map(slug => {
              probe.style.paddingTop = `var(--wp--preset--spacing--${slug})`;
              return [slug, number(getComputedStyle(probe).paddingTop)];
            }));
            probe.remove();
            const paragraphs = [...document.querySelectorAll('main p')].filter(element => element.offsetParent !== null).map(element => ratio(element));
            const headings = [...document.querySelectorAll('main h1, main h2, main h3')].filter(element => element.offsetParent !== null).map(element => ({
              level: element.tagName,
              ratio: ratio(element),
              height: element.getBoundingClientRect().height,
              clientHeight: element.clientHeight,
              scrollHeight: element.scrollHeight,
              overflowY: getComputedStyle(element).overflowY,
              text: element.textContent.trim().slice(0, 80),
            }));
            const sections = [...document.querySelectorAll('main .oc-section')].map(element => {
              const style = getComputedStyle(element);
              return { top: number(style.paddingTop), right: number(style.paddingRight), bottom: number(style.paddingBottom), left: number(style.paddingLeft) };
            });
            const content = document.querySelector('main .wp-block-post-content');
            return {
              title: document.title,
              scrollWidth: document.documentElement.scrollWidth,
              viewportWidth: innerWidth,
              spacing,
              bodyRatio: ratio(document.body),
              paragraphs,
              headings,
              sections,
              contentWidth: content ? content.getBoundingClientRect().width : null,
              expectedBodyRatio,
            };
          }, { expectedSpacing, expectedBodyRatio: isJapanese ? 1.85 : 1.75 });
          const record = { browser: name, url, width, status: response.status(), ...metrics };
          results.push(record);
          if (record.status !== 200) failures.push(`${name} ${width} ${url}: HTTP ${record.status}`);
          if (record.scrollWidth > record.viewportWidth + 1) failures.push(`${name} ${width} ${url}: horizontal overflow ${record.scrollWidth}/${record.viewportWidth}`);
          for (const [slug, expected] of Object.entries(expectedSpacing)) {
            if (Math.abs(record.spacing[slug] - expected) > 0.1) failures.push(`${name} ${width} ${url}: spacing ${slug}=${record.spacing[slug]}, expected ${expected}`);
          }
          if (Math.abs(record.bodyRatio - record.expectedBodyRatio) > 0.02) failures.push(`${name} ${width} ${url}: body ratio ${record.bodyRatio}, expected ${record.expectedBodyRatio}`);
          for (const value of record.paragraphs) {
            if (value < 1.65 || value > 2.05) failures.push(`${name} ${width} ${url}: paragraph ratio ${value}`);
          }
          for (const heading of record.headings) {
            if (heading.ratio < 1.15 || heading.ratio > 1.6) failures.push(`${name} ${width} ${url}: ${heading.level} ratio ${heading.ratio} (${heading.text})`);
            if (['hidden', 'clip'].includes(heading.overflowY) && heading.scrollHeight > heading.clientHeight + 1) failures.push(`${name} ${width} ${url}: clipped ${heading.level} (${heading.text})`);
          }
          for (const section of record.sections) {
            const vertical = width <= 781 ? 40 : 64;
            const horizontal = width <= 781 ? 16 : 24;
            if (Math.abs(section.top - vertical) > 0.1 || Math.abs(section.bottom - vertical) > 0.1) failures.push(`${name} ${width} ${url}: section vertical padding`);
            if (Math.abs(section.left - horizontal) > 0.1 || Math.abs(section.right - horizontal) > 0.1) failures.push(`${name} ${width} ${url}: section horizontal padding`);
          }
        }
      }
    } finally {
      await browser.close();
    }
  }
  fs.writeFileSync(path.join(output, 'design-metrics.json'), JSON.stringify(results, null, 2));
  fs.writeFileSync(path.join(output, 'design-metrics-summary.json'), JSON.stringify({ checks: results.length, failures }, null, 2));
  console.log(JSON.stringify({ checks: results.length, failures }, null, 2));
  if (failures.length) process.exitCode = 1;
})().catch(error => {
  console.error(error);
  process.exit(1);
});
