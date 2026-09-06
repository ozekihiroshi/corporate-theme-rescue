# Existing AWS test site showcase

Target: https://wp.ceri.link (existing wp-rescue container, not a new deployment).
Ozeki Corporate ZIP SHA256:
985fe70bb5b96b0fbe811d26b9b4c51d189ef400be5d14bae84dc88be43430c6

User confirmed successful pre-change DB backup:
s3://ceri-secure-s3-storage-test/wordpress-test/backups/database/2026/09/06/db-wp_rescue-20260906-130946.sql.gz
The backup was made by the existing plugin and was not restore-tested this turn.
Root DB dump attempt failed; do not use before.sql.gz as a database backup.
Config/themes archive: /home/ubuntu/oc-showcase-deploy.2LAuRCLd/config-themes.tar.gz

Installed and activated release ZIP. Ran deploy-aws-showcase.php with explicit
OC_SHOWCASE_TARGET=https://wp.ceri.link. Eight demo pages, three articles and
three generated photos; selected Japanese Refined and pretty permalinks.
Page 62 (services) was intentionally replaced. Its pre-change content and selected
options were retained in oc_aws_before_showcase. Other existing pages were not
deleted. Twenty Twenty-Five footer record 69 and its theme association remain.
S3 backup plugin and Plugin Check remain active; their settings were not changed.

Template fixture lookups now explicitly verify wp_theme membership to avoid
overwriting another theme's stored template parts. Windows SCP resets were
resolved by using WSL SCP.

Windows Edge external-page verification failed with ERR_CONNECTION_RESET before
the first page; no 16-case visual pass is claimed. Safari/iPhone and VoiceOver
verification remain user checks. No new site/container/subdomain was created.
No commit or push performed in this deployment turn.

## Subsequent user device checks

The user confirmed on iPhone Safari that page display, full-screen mobile menu
opening/closing, and menu link navigation worked. White menu background was
expected overlay behavior. The user then tried VoiceOver for the first time and
reported speech and page navigation appeared to work, but did not find it easy
to use. Record this as basic operation observed, not usability approval or a
comprehensive screen-reader pass. Device model and iOS version were not recorded.
Mac Safari remains untested because the available Mac is broken.

AWS-hosted HTTP checks returned 200 for all eight showcase routes. The earlier
Windows external browser failure remains an environment-limited attempt.
