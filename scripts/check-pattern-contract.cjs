// Read-only source checks; not a substitute for WordPress/editor/browser tests.
const fs = require('node:fs');
const path = require('node:path');
const assert = require('node:assert/strict');
const root = path.resolve(process.argv[2] || '../ozeki-corporate');
const sections = ['hero', 'introduction', 'services', 'strengths', 'latest-news', 'company-information', 'call-to-action', 'process', 'representative-message', 'case-study'];
for (const name of sections) {
  const source = fs.readFileSync(path.join(root, 'patterns', name + '.php'), 'utf8');
  const match = source.match(/<!-- wp:group (\{[^\n]*?\}) -->/);
  assert.ok(match, name + ': root group');
  const attrs = JSON.parse(match[1]);
  assert.ok(attrs.className.split(/\s+/).includes('oc-section'), name + ': shared section class');
  assert.equal(attrs.style?.spacing?.padding, undefined, name + ': no fixed padding override');
  for (const block of source.matchAll(/<!-- wp:[\w/-]+ (\{.*?\}) (?:\/)?-->/g)) {
    JSON.parse(block[1]);
  }
  assert.ok(source.includes('alignfull oc-section'), name + ': saved markup class');
}
const css = fs.readFileSync(path.join(root, 'style.css'), 'utf8');
assert.ok(css.includes('.oc-section { padding: var(--wp--preset--spacing--60) var(--wp--preset--spacing--40); margin-block-start: 0; }'));
assert.ok(css.includes('.oc-section { padding: var(--wp--preset--spacing--50) var(--wp--preset--spacing--30); }'));
assert.ok(!/\.oc-card-body h3\s*\{[^}]*font-size/.test(css));
assert.ok(!/\.oc-steps h3\s*\{[^}]*font-size/.test(css));
const servicePage = fs.readFileSync(path.join(root, 'patterns/page-services.php'), 'utf8');
assert.ok(servicePage.includes("patterns/process.php"));
assert.ok(!servicePage.includes('oc-steps'));
const company = fs.readFileSync(path.join(root, 'patterns/company-information.php'), 'utf8');
assert.equal((company.match(/scope="row"/g) || []).length, 6);
console.log('PATTERN_CONTRACT_OK sections=10 shared-spacing inherited-item-typography company-rows=6 extracted-process');
