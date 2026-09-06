#!/usr/bin/env bash
set -uo pipefail
cd "$(dirname "$0")/.."
results=$(mktemp -d /tmp/oc-matrix-results.XXXXXXXX)
failed=0
for php in 8.1 8.2 8.3 8.4; do
  for wp in 6.6 7.1; do
    echo "=== PHP $php / WordPress $wp ==="
    if AUDIT_PHP="$php" AUDIT_WP="$wp" docker compose -f docker-compose.audit.yml run --rm runner >"$results/php-$php-wp-$wp.log" 2>&1; then
      echo "PASS PHP=$php WP=$wp"
    else
      echo "FAIL PHP=$php WP=$wp"
      failed=1
    fi
    tail -8 "$results/php-$php-wp-$wp.log"
  done
done
echo "RESULTS=$results"
exit "$failed"
