const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');

const url = process.env.OC_STYLE_FIXTURE_URL || 'http://localhost:8090/screenshot-block-styles-fixture.php';
const executablePath = process.env.BROWSER_EXE;
if (!executablePath) throw new Error('Set BROWSER_EXE');

(async () => {
  const browser = await chromium.launch({ headless: true, executablePath });
  const failures = [];
  const results = [];
  try {
    for (const width of [1440, 390, 320]) {
      const page = await browser.newPage({ viewport: { width, height: 900 } });
      const response = await page.goto(url, { waitUntil: 'networkidle', timeout: 60000 });
      const metrics = await page.evaluate(() => {
        const keyPoint = document.querySelector('.wp-block-group.is-style-key-point');
        const image = document.querySelector('.wp-block-image.is-style-soft-shadow img');
        const button = document.querySelector('.wp-element-button');
        const keyPointStyle = getComputedStyle(keyPoint);
        return {
          scrollWidth: document.documentElement.scrollWidth,
          viewportWidth: innerWidth,
          keyPointBackground: keyPointStyle.backgroundColor,
          keyPointBorderColor: keyPointStyle.borderInlineStartColor,
          keyPointBorderWidth: keyPointStyle.borderInlineStartWidth,
          keyPointPadding: keyPointStyle.paddingTop,
          imageShadow: getComputedStyle(image).boxShadow,
          buttonBackground: getComputedStyle(button).backgroundColor,
          buttonColor: getComputedStyle(button).color,
        };
      });
      results.push({ width, status: response.status(), ...metrics });
      if (response.status() !== 200) failures.push(`${width}: HTTP ${response.status()}`);
      if (metrics.scrollWidth > metrics.viewportWidth + 1) failures.push(`${width}: horizontal overflow`);
      if (metrics.keyPointBackground !== 'rgb(243, 245, 246)') failures.push(`${width}: key point background ${metrics.keyPointBackground}`);
      if (metrics.keyPointBorderColor !== 'rgb(8, 127, 115)') failures.push(`${width}: key point border ${metrics.keyPointBorderColor}`);
      if (metrics.keyPointBorderWidth !== '4px') failures.push(`${width}: key point border width ${metrics.keyPointBorderWidth}`);
      if (metrics.keyPointPadding !== '24px') failures.push(`${width}: key point padding ${metrics.keyPointPadding}`);
      if (metrics.imageShadow === 'none') failures.push(`${width}: image shadow missing`);
      if (metrics.buttonBackground !== 'rgb(24, 59, 86)') failures.push(`${width}: button background ${metrics.buttonBackground}`);
      if (metrics.buttonColor !== 'rgb(255, 255, 255)') failures.push(`${width}: button color ${metrics.buttonColor}`);
      await page.close();
    }
  } finally {
    await browser.close();
  }
  console.log(JSON.stringify({ checks: results.length, failures, results }, null, 2));
  if (failures.length) process.exitCode = 1;
})().catch(error => {
  console.error(error);
  process.exit(1);
});
