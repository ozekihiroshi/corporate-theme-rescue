# Theme Unit Test and WebKit preliminary audit

Official data was imported into the existing isolated ZIP site at localhost:8090.
8089 demo content and theme source were not changed. Importer 0.9.6 completed
successfully (186 source items processed, authors=skip). Import completion is not
a guarantee that every remote media item was available. The selected scans found
no completed-but-broken images; pending/lazy images are not comprehensively tested.

Source: https://github.com/WordPress/theme-test-data/blob/master/themeunittestdata.wordpress.xml
Retrieved SHA256: 457aace6ec93cf77369bbcc6158996e52da8798bd5e39c83d58dfab9b50d64fa

Pre-import DB snapshot is inside the wordpress container at
`/var/oc-audit-private/before-unit-test.sql` (directory 0700, file 0600).
It was initially exported under wp-content, then moved to that private location.
Do not recreate the container before preserving this snapshot if rollback is needed.
No existing content was deleted and no automatic rollback was performed.

## Automated results

Runner: scripts/check-unit-content.cjs. Edge and Playwright WebKit 26.5/build2336,
widths 1440 and 320. WebKit is not branded Safari and this is NOT a Safari pass.

- Initial 12 articles x 2 browsers x 2 widths: 48 checks, no detected overflow,
  HTTP errors, completed broken images or JavaScript page errors.
- Additional 10 boundary articles: 40 checks, 14 overflow findings. All HTTP 200;
  no captured JavaScript errors or completed broken images.
- Boundary IDs: 1175,1169,1170,1178,1177,1171,1168,1148,1000,1134.
- 1178 (HTML formatting), 1148 (comments), 1134 (page formatting) overflow at
  both widths in both engines. Underlying cause still needs element/pseudo-element
  inspection; empty main-element overflow lists do not negate document overflow.
- 1177 (image alignment) overflows at 320px in both engines; fixed-width legacy
  wp-caption figures and their contents are visible in the overflow evidence.
- ARIA snapshots expose nested banner and nested contentinfo landmarks. Template
  parts use header/footer wrappers and their inner groups repeat those tags.

Raw records: unit-content.json and boundary/unit-content.json. This runner records
findings but currently exits successfully on detected layout findings; exit 0 is
NOT a release gate. Review the findings explicitly.

## Remaining manual and functional review

- Fix/retest duplicate landmarks and legacy-content overflow before publication.
- Actual Safari on Apple hardware; Windows WebKit is supplementary only.
- Actual screen-reader listening and navigation (NVDA/VoiceOver). ARIA snapshots
  do not establish the spoken output or usability. NVDA not found in initial
  Program Files checks; portable/user installs have not been ruled out.
- Full Theme Unit Test visual checklist and interactions, including pagination,
  password submission, comment threading/reply and keyboard focus visibility.
  Current article scans do not exercise those actions.

No theme fix, ZIP rebuild, commit, push or public release was performed.

References:
- https://make.wordpress.org/themes/handbook/review/theme-unit-test/
- https://playwright.dev/docs/browsers
