# Release audit — 2026-09-06

## Current checkpoint

The sections below retain the initial audit history. Current follow-ups:

- [Full Edge/Firefox retry](audit/2026-09-06-stage2-retry/README.md): passed.
- [Theme Unit Test findings](audit/2026-09-06-stage3/README.md) and
  [fix/retest](audit/2026-09-06-stage3-fixed/README.md): 88 cases passed after fixes.
- [Existing AWS showcase and user device checks](aws-showcase.md).
- Current fixed ZIP SHA256:
  `985fe70bb5b96b0fbe811d26b9b4c51d189ef400be5d14bae84dc88be43430c6`.
- iPhone Safari display/menu/navigation and basic VoiceOver reading/navigation
  were reported by the user. This is not full accessibility or usability approval.
- Mac Safari and the full interactive/manual checklist remain outstanding.

Follow-up: [second-stage browser, contrast and editor checks](audit/2026-09-06-stage2/README.md).

This is an initial release-readiness audit, not a claim of WordPress.org approval
or complete WCAG conformance. No public release or submission was performed.

## Reproduce

1. In the theme repository: `bash build-release.sh`.
2. In this repository: `bash scripts/run-theme-matrix.sh`.

The matrix installs the distribution ZIP, not the theme source tree, in isolated
temporary WordPress directories. Its database is an independent Compose service
with tmpfs storage and no published port. Each run uses a unique table prefix.
The runner's 512 MB PHP limit accommodates WordPress package extraction; it is
not a change to the theme runtime requirement. Logs are kept in the reported
`/tmp/oc-matrix-results.*` directory. Stop the audit database after testing with:

```sh
docker compose -f docker-compose.audit.yml stop db
```

The browser audit uses the existing 8089 sample and the independent 8090 ZIP
installation. It does not edit posts, settings, or templates. Install Playwright
and axe-core 4.10.3 as local development dependencies, then run:

```sh
node scripts/check-accessibility.cjs
```

`PLAYWRIGHT_MODULE`, `AXE_SOURCE`, and `BROWSER_EXE` may point to existing tool
installations. They are not included in the theme. The audit covers WCAG 2 A/AA
and WCAG 2.1 AA axe rules, plus skip-link and responsive-menu keyboard behavior.
It prints both violations and incomplete results; incomplete results require
manual review and must not be described as automated passes.

## Changes found by the audit

- Added the required 1200 × 900 screenshot and explicit copyright/resource
  attribution. The screenshot shows actual theme rendering with demo content.
- Replaced the 404 page's standalone Home Link block, which rendered an orphan
  list item, with a translated, escaped home button pattern.
- Placed the initial automatic Page List inside a native Pages submenu. This
  avoids nested root lists in WordPress 7.0.2 while retaining automatic page
  discovery. Existing saved navigation, including 8089's menu, is not rewritten.
- Excluded development files and normalized line endings in the distribution
  ZIP. Source files are left unchanged by the build.

## Recorded results

Distribution ZIP SHA-256:
`11f9fae413418bb104f21bc898c9dfa239516857a4e8b6066b110535de8a152f`

- PHP 8.1.34 / 8.2.33 / 8.3.33 / 8.4.25 × WordPress 6.6 / 7.1:
  all eight installation, activation and rendering smoke tests passed.
- Theme Check 20260901: no REQUIRED or WARNING findings; one INFO confirms
  the sole text domain `ozeki-corporate`.
- axe-core 4.10.3: 26 page/viewport scans, zero detected violations.
- Two incomplete color-contrast results remain on the layered default hero
  (desktop and mobile). These are not counted as passes; inspect visually before
  submission. The palette calculation for white text over the 70% `#17212b`
  overlay on white is approximately 6.04:1, but this does not replace review of
  the rendered composition or user-selected background images.
- Both sites: visible first-tab skip link, next keyboard focus inside main
  content, mobile menu opening, Escape closing and focus return passed.
- Raw logs: [matrix](audit/2026-09-06/matrix/),
  [accessibility](audit/2026-09-06/accessibility.json).

## Coverage limitations

The PHP/WordPress matrix is representative boundary coverage: PHP 8.1, 8.2,
8.3 and 8.4 with WordPress 6.6 and 7.1. It checks installation, activation,
PHP syntax, JSON loading, template/pattern rendering and Theme Check. It is not
a full interactive browser test on every intermediate WordPress version.

Browser checks use WordPress 7.0.2/PHP 8.3 on 8089 and 8090, at 1440px and
390px. Full screen-reader testing, Safari/Firefox coverage, translated UI review,
Theme Unit Test content and broader editor/revision checks remain release work.
The theme does not claim the `accessibility-ready` tag.

References:
- https://make.wordpress.org/themes/handbook/review/required/
- https://make.wordpress.org/themes/handbook/review/accessibility/
- https://github.com/WordPress/theme-check
