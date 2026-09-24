# Ozeki Corporate 0.2.3 review corrections

Response to requirements in Theme Trac ticket 290666, comment 3. The reviewed
submission was 0.2.0; these changes build on the unreleased 0.2.2 candidate.
Both repositories use branch `codex/review-fixes-0.2.3`.

## Changes

- The page template now includes native comments, reply links, comment
  pagination and the comment form. Closed comments preserve existing comments;
  password-protected pages do not expose comments before authentication.
- Initial navigation uses Home followed by the native Page List directly,
  preserving published page hierarchies without an extra Pages submenu.
- Template interface strings moved into non-insertable PHP patterns. This
  covers navigation, 404, search, empty results, excerpt links and footer credit.
- Theme translations can load from `languages`, and the ZIP includes
  `languages/ozeki-corporate.pot`. `update-pot.sh` regenerates the catalog with
  WP-CLI. This does not overwrite saved user content or custom templates.
- Setup guidance and version metadata were updated.

## Tested artifact

- Theme repository path: `build/review-0.2.3/ozeki-corporate-0.2.3.zip`
- SHA-256: `38d338bd4b0b29302790f6cda4289732f16ae46526dc1b89e5189d264b4e6b47`
- This is a test artifact for steps 1–5, not a WordPress.org upload or release.

## Compatibility and Theme Check

All eight combinations passed: PHP 8.1, 8.2, 8.3 and 8.4, each with WordPress
6.6 and 7.1. Raw logs: `/tmp/oc-matrix-results.ELfG5Ltt`; exit code 0.
Every case included PHP lint, runtime/template rendering, design-system checks,
pattern serialization, guide checks, the new comment/translation checks, and
Theme Check. Theme Check 20260901 returned only the expected text-domain INFO.

`check-review-runtime.php` checks open, closed, empty and protected page comments.
It also creates a temporary local translation catalog and verifies six patterns
actually use translated strings and still serialize correctly. Test posts and
the temporary catalog are removed afterward in the disposable audit installation.

The initial matrix attempt encountered terminal job-control suspension after a
completed case. The runner now uses `timeout --foreground` and closed stdin;
the complete eight-case run above used this corrected runner.

## Browser checks

8090 uses WordPress 7.0.2 / PHP 8.3. The installed ZIP is tested with a temporary
localhost-only endpoint that renders its file templates, bypassing saved demo
templates. Existing site templates, navigation and content are not reset.

`check-review-browser.cjs` covers automatic page hierarchies and explicitly
configured Navigation submenus, each at 1440, 390 and 320 CSS pixels:

- parent/child/grandchild hover and keyboard traversal;
- Tab, Shift+Tab, Enter, desktop Escape/Space and mobile Escape/focus return;
- navigation to the grandchild page;
- existing page comments, reply and cancel controls;
- closed/protected comment visibility and horizontal overflow;
- screenshots in `.audit-tools/review-0.2.3/` (local, ignored).

Keys use a 100ms press duration, and expansion assertions await the state change.
Zero-delay key sequences were unstable while core navigation updated its state.
These are Chromium/Edge automated checks and screenshot inspection; they do not
claim a new Safari/VoiceOver run or a comprehensive accessibility certification.

## Reproduce locally

1. Build the theme ZIP and install it on the existing 8090 environment.
2. Mount `scripts` as `/audit` in the WP-CLI container and run
   `wp eval-file /audit/seed-review-fixture.php`.
3. Copy `scripts/review-fixture.php` to the 8090 document root.
4. Run `check-review-browser.cjs` with `PLAYWRIGHT_MODULE`, `BROWSER_EXE` and
   `OC_AUDIT_OUTPUT` pointing to local development tools/output.
5. Run `wp eval-file /audit/seed-review-fixture.php cleanup` and remove only
   `/var/www/html/review-fixture.php` from the test WordPress container.
6. Run the matrix with
   `AUDIT_ZIP=review-0.2.3/ozeki-corporate-0.2.3.zip bash scripts/run-theme-matrix.sh`.

The next release steps remain final ZIP handoff and WordPress.org resubmission.
