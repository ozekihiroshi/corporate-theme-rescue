# Distribution onboarding verification — 2026-09-07

## Scope

Optional Appearance > Ozeki Corporate Guide, four native page starter patterns,
navigation instructions and an English/Japanese pre-publication checklist.
The guide is read-only: no activation redirect, demo import, option writes or
automatic page creation. Pages are created only through the standard editor.

## Artifact and environment

- Local isolated site: http://localhost:8091/ (WordPress 7.0.2 / PHP 8.3).
- ZIP: `.audit-tools/onboarding-release/ozeki-corporate-0.1.0.zip`.
- SHA-256: `bdb6f22137a29d0c0c2a51430f73f67f8ab90279d3332c4d90cf58b4c56ec5bf`.
- This is a development artifact, not the published GitHub v0.1.0 ZIP and not
  a finalized public release candidate. The existing release/tag was not changed.
- Installed from the ZIP; no theme source bind mount.

## Results

- PHP lint: all pattern files and the new admin guide pass.
- Nine starter/section patterns parse and serialize without alteration in PHP.
- All four new page patterns register for core/post-content and page post type.
- All four parse without invalid blocks in the WordPress editor.
- Appearance menu opens the guide; Japanese guide anchor works.
- Guide links are local to the test site (no external guide service).
- Add a page opens the native chooser with About, Company, Contact and Services.
- UI selection of About, title entry, Save draft and editor reload pass.
- Saved test page: ID 11, `Onboarding check – About`, status draft.
  Body content survives reload; nothing was published.
- English guide and native pattern chooser screenshots visually reviewed.
- `git diff --check` passes for the theme.

Evidence: `.audit-tools/onboarding/` contains guide screenshots,
page-chooser.jpg, pattern-validation.json, saved-draft.json and saved-draft.jpg.
Scripts: `scripts/check-starter-patterns.php` and `scripts/check-onboarding-ui.cjs`.
The browser script requires PLAYWRIGHT_MODULE, BROWSER_EXE, OC_AUDIT_OUTPUT and
OC_STARTER_PASSWORD and targets only the disposable local 8091 test account.

## Boundaries and follow-up

- The saved-page UI journey was performed for About; the other three patterns
  received registry and block validation, not separate end-to-end publishing.
- Navigation and the launch checklist were added as instructions. This run
  did not change or publish menus, test form delivery or certify accessibility.
- Full Theme Check, release compatibility matrix, updated translation catalogs
  and Safari regression remain release-candidate checks.
- AWS Japanese demo, AWS preview page, local 8089/8090 content and GitHub releases
  were not modified. No commit or push was performed.
