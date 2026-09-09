# Guide update candidate — 2026-09-09

Artifact: `D:/workspace/ozeki-corporate/build/rc-2026-09-09/ozeki-corporate-0.2.0.zip`

SHA-256: `82b716afb313ba2088650b04c9c50bfd1c0eef1fcd798410bf0f56c30e4b9cb4`

Size: 878,861 bytes. Header version remains 0.2.0, not yet released. Earlier
candidate in build/ is preserved; this dated subdirectory is the guide-update
candidate. Do not confuse the artifacts.

Passed: ZIP integrity; 32 packaged files matched source byte-for-byte after the
build's text CRLF normalization; expected top-level directory; no development
docs/scripts/build/node_modules/Git entries; PHP lint on all packaged PHP files;
git diff --check. Includes the new English/Japanese Pages-versus-Posts guide and
the standard admin links for writing/reviewing posts.

BLOCKED: Docker in Ubuntu-24.04 reports activating (containerd active), but Docker
API ping/info and listing containers do not respond within diagnostic bounds.
Disk space is available. Root cause is not established. No forced Docker/WSL
restart was performed because it may affect unrelated local environments.

Installed-ZIP guide link/UI verification, Theme Check and the compatibility matrix
have NOT run on this candidate. Previous candidate results do not constitute a
pass for this ZIP. Resume with this exact artifact after Docker recovery. For the
existing matrix runner set AUDIT_ZIP=rc-2026-09-09/ozeki-corporate-0.2.0.zip.

AWS, existing site content, commits, tags and releases were not changed.

## Recovery and completed verification

WSL was shut down/restarted with user authorization. Docker initialization was
slow and WSL initialization timeouts occurred; a temporary keepalive session was
used during verification. Docker 29.1.3 eventually became active and API-responsive.
No Docker volumes/containers were deleted. Root cause of the delay is not proven.
An unrelated demand-monitor-redis-1 container remains in a restart loop; not modified.

The blocked checks above were subsequently completed against this same ZIP:

- All eight PHP 8.1–8.4 / WordPress 6.6 and 7.1 cases passed with exit code 0.
- Fresh isolated installations, PHP lint, runtime templates, starter pattern
  parsing/serialization and new English/Japanese guide-link checks passed.
- Theme Check 20260901: no non-INFO findings in all eight runs. The sole INFO
  confirms the single text domain ozeki-corporate.
- Raw matrix logs are in docs/audit/2026-09-09-guide-candidate/.
- ZIP installed into existing isolated 8091 site, without a theme source bind mount.
  Browser test verifies English content, Japanese anchor/content, exact new-post
  link destination, navigation into Posts list, and the packaged full guide.
  New-post link was checked by href, not by creating/publishing content.
- English/Japanese screenshots visually reviewed. Evidence is in
  .audit-tools/guide-rc-2026-09-09/. Temporary test administrator (ID 2) deleted;
  no posts created, changed or deleted by the guide test.

This is proportional final verification of the guide changes, not a new complete
accessibility/Safari regression or all-user journey test. The previous manual
Safari record and its untested boundaries remain unchanged. Existing AWS site
was not updated; no commit, push, tag or release was created.
