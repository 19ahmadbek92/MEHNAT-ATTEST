#!/bin/bash
set -euo pipefail

ROLE="${APP_PROCESS_ROLE:-app}"

prepare_app() {
  php artisan storage:link 2>/dev/null || true
  php artisan config:cache
  php artisan route:cache
  php artisan view:cache
  php artisan event:cache
}

run_migrations_if_needed() {
  if [ "${RUN_MIGRATIONS:-true}" = "true" ]; then
    php artisan migrate --force
  fi
}

case "$ROLE" in
  app)
    run_migrations_if_needed
    prepare_app
    exec /entrypoint supervisord
    ;;
  worker)
    prepare_app
    exec php artisan queue:work --verbose --tries=3 --timeout=120
    ;;
  scheduler)
    prepare_app
    exec php artisan schedule:work
    ;;
  *)
    echo "Unknown APP_PROCESS_ROLE: $ROLE"
    exit 1
    ;;
esac
