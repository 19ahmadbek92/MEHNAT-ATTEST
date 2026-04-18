#!/bin/bash
set -euo pipefail

# Worker/scheduler uchun: Render yoki docker-compose "Docker Command" / command sifatida.
ROLE="${APP_PROCESS_ROLE:-app}"

cd /var/www/html || exit 1

artisan() {
  if command -v gosu >/dev/null 2>&1 && id -u application >/dev/null 2>&1; then
    gosu application php artisan "$@"
  else
    php artisan "$@"
  fi
}

prepare_app() {
  artisan storage:link 2>/dev/null || true
  artisan config:cache
  artisan route:cache
  artisan view:cache
  artisan event:cache
}

case "$ROLE" in
  worker)
    prepare_app
    if command -v gosu >/dev/null 2>&1 && id -u application >/dev/null 2>&1; then
      exec gosu application php artisan queue:work --verbose --tries=3 --timeout=120
    fi
    exec php artisan queue:work --verbose --tries=3 --timeout=120
    ;;
  scheduler)
    prepare_app
    if command -v gosu >/dev/null 2>&1 && id -u application >/dev/null 2>&1; then
      exec gosu application php artisan schedule:work
    fi
    exec php artisan schedule:work
    ;;
  *)
    echo "laravel-role-cmd: APP_PROCESS_ROLE=worker yoki scheduler bo'lishi kerak (hozir: $ROLE). Web uchun CMD=supervisord ishlating."
    exit 1
    ;;
esac
