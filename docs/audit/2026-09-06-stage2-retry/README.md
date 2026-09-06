# Full browser regression retry

The resource-interrupted Edge/Firefox comparison from stage 2 was rerun after
the user freed disk space. C: had 3,591,499,776 bytes available before the run.
Browser binaries and temporary files remained on D:.

Command: `scripts/check-browser-regression.cjs`, with `OC_BROWSERS=edge,firefox`,
`OC_QUICK` unset and `OC_FIXTURE_URL=http://localhost:8090/?page_id=5`.
The process completed with exit code 0.

## Results

- 36 page checks: two browsers, three widths (1440, 390, 320), six URLs.
- URLs: 8089 home, design-guide, english; 8090 search, home, fixture page 5.
- All responses HTTP 200; no document horizontal overflow, broken images or
  captured JavaScript page errors.
- Both browsers: desktop submenu opened with Enter; mobile menu opened with
  Enter, closed with Escape and returned focus at both mobile widths.
- DOM color measurements agree with the previous initial hero contrast review.

Raw results are in `browser-regression.json`; the two JPEG files are captured
default hero screenshots, not a pixel-difference comparison.

No demo content, theme code or settings were changed. No commit or push was
performed. This resolves the full browser comparison resource blocker, not the
remaining Safari, screen-reader, Theme Unit Test or broader authoring checks.
