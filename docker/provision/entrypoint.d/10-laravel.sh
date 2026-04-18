#!/bin/bash
set -euo pipefail

# Faqat web (Nginx + PHP-FPM) konteynerida migratsiya va cache — worker/schedulerda yo'q.
ROLE="${APP_PROCESS_ROLE:-app}"
if [ "$ROLE" != "app" ]; then
  exit 0
fi

cd /var/www/html || exit 1

artisan() {
  if command -v gosu >/dev/null 2>&1 && id -u application >/dev/null 2>&1; then
    gosu application php artisan "$@"
  else
    php artisan "$@"
  fi
}

if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
  artisan migrate --force
fi

artisan storage:link 2>/dev/null || true
artisan config:cache
artisan route:cache
artisan view:cache
artisan event:cache
