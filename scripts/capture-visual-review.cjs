const { chromium } = require(process.env.PLAYWRIGHT_MODULE || 'playwright');
const fs = require('fs');
const path = require('path');

const output = process.env.OC_AUDIT_OUTPUT;
const executablePath = process.env.BROWSER_EXE;
if (!output) throw new Error('Set OC_AUDIT_OUTPUT to an evidence directory');
if (!executablePath) throw new Error('Set BROWSER_EXE to the Chromium or Edge executable');

const targets = [
  {
    name: 'english-starter',
    url: process.env.OC_ENGLISH_URL || 'http://localhost:8090/screenshot-english-fixture.php',
  },
  {
    name: 'japanese-refined',
    url: process.env.OC_JAPANESE_URL || 'http://localhost:8090/?page_id=5&oc_japanese_refined=1',
  },
  {
    name: 'color-block-styles',
    url: process.env.OC_BLOCK_STYLES_URL || 'http://localhost:8090/screenshot-block-styles-fixture.php',
  },
];
const viewports = [
  { name: 'desktop', width: 1440, height: 1000 },
  { name: 'mobile', width: 390, height: 844 },
];

(async () => {
  fs.mkdirSync(output, { recursive: true });
  const browser = await chromium.launch({ headless: true, executablePath });
  try {
    for (const target of targets) {
      for (const viewport of viewports) {
        const page = await browser.newPage({ viewport });
        const response = await page.goto(target.url, { waitUntil: 'networkidle', timeout: 60000 });
        if (!response || response.status() !== 200) {
          throw new Error(`${target.name} ${viewport.name}: HTTP ${response ? response.status() : 'no response'}`);
        }
        await page.evaluate(async () => {
          await document.fonts.ready;
          await Promise.all([...document.images].map(image => image.decode().catch(() => {})));
        });
        await page.screenshot({
          path: path.join(output, `${target.name}-${viewport.name}-viewport.png`),
        });
        await page.screenshot({
          path: path.join(output, `${target.name}-${viewport.name}-full.png`),
          fullPage: true,
        });
        await page.close();
      }
    }
  } finally {
    await browser.close();
  }
})().catch(error => {
  console.error(error);
  process.exit(1);
});
