# AWS starter preview

- URL: https://wp.ceri.link/theme-starter-preview/
- Existing WordPress/container: wp-rescue. No new server, container or domain.
- Preview page ID 100; theme-scoped custom template ID 99, slug starter-preview.
- Page contains expanded starter patterns, editable as normal page blocks. No
  duplicate page-title heading in its assigned Ozeki Corporate template.
- Current global styles and header/footer customizations are shared with the
  Japanese demo. This is NOT a pristine/default-settings installation test.
- Existing homepage remains page 84; no navigation links were automatically added.

## Deployment

- Development ZIP hash:
  8070b6b3a4ca802c54ece6cec10e3e8891d3e536ed2bd68c7f5256638e2b24d5
- Local artifact: .audit-tools/starter-release-followup/ozeki-corporate-0.1.0.zip
- The header version is still 0.1.0, but this is not the published release ZIP.
- Updated existing theme via WP-CLI theme install --force, without changing the
  active theme. Old theme archived before update.
- Remote private backup directory:
  /home/ubuntu/oc-starter-preview.7l1uIDY7/
- theme-before.tar.gz passes gzip integrity check; content-before.json records
  pre-addition posts and selected options. This is not a full database backup.
- scripts/add-aws-starter-preview.php refuses an existing preview slug; 37 old
  posts were hash-compared unchanged after addition. Homepage options, selected
  theme and active plugin list were unchanged.
- Active plugins remain Plugin Check and secure-s3-storage/secure-s3-storage.php.

## Checks and switching scope

AWS-hosted curl fetched both homepage and preview successfully. Preview has one
H1 with the expected English title. Bundled team image returned HTTP 200.
Remote browser/device visual checks remain user checks; no new Safari pass claimed.

Theme switching affects the entire site, including the Japanese demo. It does
not isolate to this page. No switch or deletion was performed by the deployment.
The user can first switch to an installed Twenty Twenty theme, then reactivate
Ozeki Corporate. A theme deletion test needs this exact development ZIP available
for reinstall. Bundled image URLs depend on the theme directory and are unavailable
while the theme is deleted. Existing saved templates/styles are theme-associated;
check them after reactivation instead of assuming a fresh-install appearance.
The preview page's content can be edited under Pages; the shared header/footer
and styles remain Site Editor settings. Changes here do not prove automatic
replacement of stored content by future theme updates.

GitHub releases/tags and WordPress.org were not changed. No commit/push performed.
