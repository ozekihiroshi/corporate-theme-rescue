# AWS onboarding update — 2026-09-07

Updated only the existing Ozeki Corporate theme in `wp-rescue` on the authorized
AWS demo. No theme activation, demo seeding, new containers, pages or domains.

- Development ZIP SHA-256:
  `2d19049c1a18f091efc9d95b23a58f4b1da2b0e45f4359dddcb09bbc578b0815`.
- Local artifact: `.audit-tools/aws-onboarding-release/ozeki-corporate-0.1.0.zip`.
- Still a development artifact, not the original published GitHub v0.1.0.
- Private remote checkpoint: `/home/ubuntu/oc-onboarding-update.CwCs2eqs/`.
- `theme-before.tar.gz` passes gzip integrity checking. This archive is a theme
  rollback source, not a database backup; do not overwrite the live theme without
  inspecting any changes made after this checkpoint.
- `before.json` and `after.json` match exactly: all 39 posts (including saved
  templates/styles), 40 postmeta rows, 20 term relationships and selected options.
  Selected options include home/reading settings, active theme/plugins and theme
  mods. These are content hashes, not full database exports.
- Uploaded ZIP hash matched the local artifact before installation.
- WP-CLI `theme install --force` completed successfully without `--activate`.
- Installed English screenshot SHA-256:
  `344d1f495ec86f0a5bb9416edc4d711ab60bff96fd722e35cecc1e0df38fc79f`.
- Both `/` and `/theme-starter-preview/` return HTTP 200 after update.

Guide and patterns were previously verified locally on 8091. This deployment
does not claim a new AWS authenticated browser walkthrough, Safari pass or
complete accessibility test. The screenshot fixture was not deployed to AWS.
English screenshot is 1200 x 900, captured from real bundled default patterns;
the sample site title was request-scoped and no existing page content was reset.

Environment README now explains local site roles, ZIP installation, fixture
safety, credentials, evidence, AWS boundaries and non-destructive stop/resume.
No GitHub Actions workflows exist at this checkpoint; push success is not CI.
