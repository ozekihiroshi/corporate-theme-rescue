#!/bin/sh
set -eu
wp() { php -d memory_limit=512M /usr/local/bin/wp "$@"; }
work=$(mktemp -d /tmp/oc-theme-audit.XXXXXXXX)
cd "$work"
wp core download --version="$AUDIT_WP" --quiet
prefix="oc_$(date +%s)_$(basename "$work" | tr -cd 'A-Za-z0-9')_"
wp config create --dbname=audit --dbuser=audit --dbpass=local-audit-only --dbhost=db --dbprefix="$prefix" --quiet
wp core install --url=http://audit.invalid --title='Theme audit' --admin_user=audit_admin --admin_password=local-audit-only --admin_email=audit@example.invalid --skip-email --quiet
wp theme install "/release/${AUDIT_ZIP:?Specify AUDIT_ZIP}" --activate
find wp-content/themes/ozeki-corporate -name '*.php' -exec php -l {} \;
wp eval-file /audit/check-theme-runtime.php
wp eval-file /audit/check-starter-patterns.php
wp eval-file /audit/check-guide-runtime.php
wp plugin install theme-check --activate --quiet
wp plugin get theme-check --field=version
wp theme-check run ozeki-corporate --format=json > theme-check.json
cat theme-check.json
php /audit/check-theme-check-result.php theme-check.json
