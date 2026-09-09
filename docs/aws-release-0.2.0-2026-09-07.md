# AWS 0.2.0 deployment — 2026-09-07

Explicitly authorized update of the existing https://wp.ceri.link site in
container wp-rescue. Installed the exact candidate validated in
[0.2.0 validation](release-0.2.0-validation.md).

- ZIP SHA-256: `694b832d8469057becb60ac835554f330456ff6ceca0d0b9049ad31727960c3f`.
- Transfer hash matched before installation.
- Private remote checkpoint: `/home/ubuntu/oc-release-020.5EZNPJys/`.
- Old theme preserved as `theme-before.tar.gz`; gzip integrity check passed.
  This is a theme rollback archive, not a full database backup.
- `before.json` and `after.json` match exactly: 39 posts, 40 postmeta rows,
  20 term relationships and selected home/reading/theme/plugin options.
- WP-CLI reports active Ozeki Corporate 0.2.0 (previous header was 0.1.0).
- `/`, `/theme-starter-preview/` and the bundled `assets/images/team.png`
  all return HTTP 200. Image response size: 366,984 bytes.

No theme switch, seed/import, new pages, container or domain. Existing Japanese
demo, saved templates/styles, navigation and active plugins were preserved.
No new authenticated UI, Safari or accessibility pass is claimed by these HTTP
checks. No Git commit/push, tag, Release or WordPress.org submission was made.

AWS now has the 0.2.0 candidate; the earlier validation document's statement
that AWS retained the development version describes its pre-deployment checkpoint.
