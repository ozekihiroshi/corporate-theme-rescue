#!/usr/bin/env bash
# One-shot deployment of the validated 0.3.0 artifact to the existing demo.
set -euo pipefail
umask 077
checkpoint=${1:?Private checkpoint directory required}
[[ "$checkpoint" == /home/ubuntu/oc-release-030.* && -d "$checkpoint" ]]
cd "$checkpoint"
expected=8b494ed5ebe7f67860815013245e302ae652faaea55ef2e4b845f112237f8252
printf '%s  %s\n' "$expected" ozeki-corporate-0.3.0.zip | sha256sum -c -
docker cp check-aws-theme-state.php wp-rescue:/tmp/oc-state-030.php
docker exec wp-rescue php /tmp/oc-state-030.php > before.json
docker exec wp-rescue php /tmp/oc-wp-cli.phar theme get ozeki-corporate --field=version --allow-root > version-before.txt
docker exec wp-rescue tar -czf - -C /var/www/html wp-config.php wp-content/themes/ozeki-corporate > theme-config-before.tar.gz
gzip -t theme-config-before.tar.gz
# Credentials stay inside the database container; never print or persist them.
docker exec wp-rescue-db sh -c 'MYSQL_PWD="$MARIADB_PASSWORD" mariadb-dump --user="$MARIADB_USER" --single-transaction --quick --skip-lock-tables "$MARIADB_DATABASE"' | gzip > database-before.sql.gz
gzip -t database-before.sql.gz
zcat database-before.sql.gz | tail -5 | grep -q 'Dump completed'
sha256sum theme-config-before.tar.gz database-before.sql.gz > backup-sha256.txt
echo BACKUP_OK
docker cp ozeki-corporate-0.3.0.zip wp-rescue:/tmp/ozeki-corporate-0.3.0.zip
docker exec wp-rescue php /tmp/oc-wp-cli.phar theme install /tmp/ozeki-corporate-0.3.0.zip --force --allow-root
docker exec wp-rescue php /tmp/oc-state-030.php > after.json
diff -u before.json after.json
version=$(docker exec wp-rescue php /tmp/oc-wp-cli.phar theme get ozeki-corporate --field=version --allow-root)
[[ "$version" == 0.3.0 ]]
printf 'DEPLOY_OK version=%s content_and_selected_settings_unchanged\n' "$version"
