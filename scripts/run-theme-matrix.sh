#!/usr/bin/env bash
set -euo pipefail
cd "$(dirname "$0")/.."
results=$(mktemp -d /tmp/oc-matrix-results.XXXXXXXX)
echo "RESULTS=$results"
export AUDIT_ZIP=${AUDIT_ZIP:-ozeki-corporate-0.2.0.zip}
test -f "../ozeki-corporate/build/$AUDIT_ZIP"
sha256sum "../ozeki-corporate/build/$AUDIT_ZIP" | tee "$results/artifact.txt"
# Separate DB readiness from each runner; do not leave an unbounded startup wait.
docker compose -f docker-compose.audit.yml up -d --wait --wait-timeout 120 db
failed=0
for php in 8.1 8.2 8.3 8.4; do
  for wp in 6.6 7.1; do
    echo "=== PHP $php / WordPress $wp ==="
    if AUDIT_PHP="$php" AUDIT_WP="$wp" timeout 600 docker compose -f docker-compose.audit.yml run --rm --no-deps -T runner >"$results/php-$php-wp-$wp.log" 2>&1; then
      echo "PASS PHP=$php WP=$wp"
      printf 'PASS PHP=%s WP=%s\n' "$php" "$wp" >> "$results/summary.txt"
    else
      echo "FAIL PHP=$php WP=$wp"
      printf 'FAIL PHP=%s WP=%s\n' "$php" "$wp" >> "$results/summary.txt"
      failed=1
    fi
    tail -8 "$results/php-$php-wp-$wp.log"
  done
done
echo "RESULTS=$results"
echo "$failed" > "$results/exit-code.txt"
exit "$failed"
