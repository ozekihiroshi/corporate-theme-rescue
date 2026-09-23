# Public release and AWS update — 2026-09-10 JST

- Published https://github.com/ozekihiroshi/ozeki-corporate/releases/tag/v0.2.0
- Annotated tag v0.2.0 resolves to b779cd2a0176b772723a9ea91ddec39680108c1d.
- Attached installation ZIP: ozeki-corporate-0.2.0.zip, 878,861 bytes.
- Downloaded the public asset again; SHA-256 matches the tested candidate:
  82b716afb313ba2088650b04c9c50bfd1c0eef1fcd798410bf0f56c30e4b9cb4.
- Existing v0.1.0 was not changed. At this checkpoint, no WordPress.org
  submission had been made.

The AWS target was the existing authorized WordPress demo at
https://wp.ceri.link. No other server, site, container or domain was changed.

A private server-side rollback archive was created and passed gzip integrity
checking before deployment. It preserves the prior installed theme; it is not a
full database backup and is intentionally not stored in this repository.

Uploaded hash matched before wp theme install --force. No activation, content
seeding, new containers or configuration changes. Active theme remains 0.2.0;
the ZIP hash distinguishes this guide update from the earlier 0.2.0 candidate.

before.json and after.json match: 44 posts, 46 postmeta rows, 22 term relationships,
plus selected home/reading/theme/plugin settings. Includes saved templates/styles
in posts. The runtime English/Japanese guide/link check passed on AWS.

HTTP 200 verified for /, /theme-starter-preview/, /about/, /services/, /company/,
/contact/ and the packaged GETTING-STARTED.md. This is not a new authenticated
browser/Safari/accessibility pass. Existing local guide UI and user Safari evidence
remain in their separate reports.

No additional source change or tag move was required for this deployment.
