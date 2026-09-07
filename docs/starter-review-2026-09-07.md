# Fresh ZIP starter review — 2026-09-07

## Isolation and artifact

- Local URL: http://localhost:8091/ (loopback binding only).
- Compose: docker-compose.starter.yml; dedicated oc-starter-review html/db volumes.
- WordPress 7.0.2, wordpress:php8.3-apache, mariadb:11.
- No theme source mount, demo seeder, old database or old template settings.
- ZIP copied into .audit-tools/starter-release and installed with WP-CLI.
- SHA-256: 05bc65c1ee2684f107a40f0c5569d3f6a0543bc757cf376e858c02130ccc6d4d.
- Development ZIP still carries 0.1.0; it is NOT the published v0.1.0 artifact.
  Do not upload it as a replacement for that release.
- Existing 8089, 8090 and AWS sites were not edited in this test.

## Observations

Headless Edge/Playwright loaded the actual public page at widths 1440, 390 and
320. The bundled team image loaded at each width; document scroll width equaled
viewport width. Desktop and mobile full-page captures were visually inspected.
Initial pages contain only the theme patterns and standard WordPress initial
content (including Hello world), not the Northstar showcase pages.

The Site Editor opened Front Page. Tests used contenteditable UI fields, the
Image block toolbar, Replace > Upload and its file chooser, Alternative text,
and the normal Save button. No REST content writes or editor-store mutation
were used to substitute for these editing actions.

- Heading changed to "Clear advice. Practical progress."
- Lead changed to mixed Japanese/English text.
- Bundled team image replaced with the environment's fictional monitoring.png.
- Alternative text changed after upload completed.
- Editor reloaded: changed text, uploaded image and alternative text persisted.
- No block recovery prompt was observed.
- Public page showed changed text/image at all three widths without overflow.

The first image selected (solar.png, over 2 MiB) exceeded this clean PHP
environment's upload_max_filesize=2M. The test continued with monitoring.png
within that limit; server upload limits were not increased. An initial locator
targeting the inner img was corrected to select the Image block. The first
save was subsequently checked in a separate reopened editor session. The final
test verifies persistence, not merely the click on Save.

## Evidence and retained state

Ignored local evidence: .audit-tools/starter-review/
- initial-results.json; initial-1440.jpg, initial-390.jpg, initial-320.jpg
- edit-results.json; edited-1440.jpg, edited-390.jpg, edited-320.jpg
- editor-reopened.jpg

scripts/check-starter-ui.cjs contains the UI test. Set PLAYWRIGHT_MODULE,
BROWSER_EXE, OC_AUDIT_OUTPUT and OC_STARTER_PASSWORD. OC_VERIFY_ONLY=1 verifies
the already edited fixture, whereas the full path expects original starter copy.
Do not run against another site: it intentionally targets localhost:8091.
The local admin is starter_review; credentials are not stored in this report.

8091 is retained in the edited state for inspection; initial captures preserve
the fresh-install appearance. This test does not assert theme source changes
automatically replace previously saved template customizations.

## Remaining product issues (not certified complete)

1. Company table row labels wrap inside words on narrow screens.
2. The contact area has text but no usable contact destination; add a clear
   user setup path without inventing contact details or implying a built-in form.
3. Default Hello world / Sample Page content needs explicit onboarding guidance,
   not silent deletion by the theme.
4. Image instructions should cover upload size limits, crop/aspect ratio and
   setting alternative text AFTER replacement completes.
5. Supporting-page patterns, navigation setup and Japanese Refined selection
   were not exercised in this pass.
6. This was Edge only, not a new Mac Safari or comprehensive accessibility test.
