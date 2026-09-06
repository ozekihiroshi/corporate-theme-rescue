# Fix and retest

Rebuilt ZIP SHA256:
`985fe70bb5b96b0fbe811d26b9b4c51d189ef400be5d14bae84dc88be43430c6`

Installed this ZIP over the existing theme on the isolated 8090 site, retaining
official Theme Unit Test data. No content or settings were changed on 8089;
its read-only source mount naturally reflects the theme code changes.

## Fixes

- Inner header/footer groups are divs; outer template-part landmarks remain.
- Classic caption figures are constrained to the content width; content/comment
  images have max-width 100% and automatic height.
- Classic preformatted content preserves whitespace/newlines but wraps long lines
  (`pre-wrap`, `overflow-wrap:anywhere`). Long ASCII-art lines may therefore wrap;
  the page is not clipped with global overflow suppression.
- Test runner now exits nonzero for layout/image/HTTP/JS findings or duplicate
  header/footer elements, instead of only printing findings.

## Retest

- Edge and Playwright WebKit, 22 articles x 2 widths (1440, 320): 88 checks.
- Exit 0, zero recorded findings, no duplicate header/footer elements.
- Raw results and ARIA snapshots are in this directory.
- Visually inspected classic caption/image alignment on 1177 at 320px:
  image and caption fit, smaller floated image retains surrounding text flow.
- Theme Check 20260901: only INFO for correct sole text domain ozeki-corporate;
  no REQUIRED/WARNING findings. WP-CLI emitted a non-fatal cache-directory warning
  during checker installation.

This does not substitute for actual Safari or screen-reader testing, or the full
interactive Theme Unit Test checklist. Those scope limitations remain.
No commit, push, tag or public release was performed.
