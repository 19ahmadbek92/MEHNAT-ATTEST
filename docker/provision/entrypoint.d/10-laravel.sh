#!/bin/bash
# Laravel boot: diagnostika, ruxsatlar, DB kutish, migratsiya, cache.
# ERRORni yashirmaymiz — Renderda xato sabablari aniq ko'rinishi shart.
set -uo pipefail

ROLE="${APP_PROCESS_ROLE:-app}"

log() {
    printf '\e[1;36m[laravel-boot]\e[0m %s\n' "$*"
}
err() {
    printf '\e[1;31m[laravel-boot][ERROR]\e[0m %s\n' "$*" >&2
}

log "role=${ROLE} env=${APP_ENV:-unset} debug=${APP_DEBUG:-unset} url=${APP_URL:-unset}"

# worker/scheduler rolda migratsiya/cache ishlamaydi — bu web rolga tegishli.
if [ "$ROLE" != "app" ]; then
    log "non-web role — bootstrap o'tkazib yuborildi."
    exit 0
fi

cd /var/www/html || { err "/var/www/html topilmadi"; exit 1; }

artisan() {
    if command -v gosu >/dev/null 2>&1 && id -u application >/dev/null 2>&1; then
        gosu application php artisan "$@"
    else
        php artisan "$@"
    fi
}

# ---- 1. Fayl tizimi ruxsatlari (har deployda defensiv) ----
ensure_writable_paths() {
    log "writable paths: storage, bootstrap/cache, database"
    mkdir -p \
        /var/www/html/storage/logs \
        /var/www/html/storage/framework/sessions \
        /var/www/html/storage/framework/views \
        /var/www/html/storage/framework/cache/data \
        /var/www/html/bootstrap/cache \
        /var/www/html/database || true

    if id -u application >/dev/null 2>&1; then
        chown -R application:application \
            /var/www/html/storage \
            /var/www/html/bootstrap/cache \
            /var/www/html/database 2>/dev/null || true
    fi
    chmod -R ug+rwX \
        /var/www/html/storage \
        /var/www/html/bootstrap/cache \
        /var/www/html/database 2>/dev/null || true
}

# ---- 2. SQLite fayli (agar ishlatilsa) ----
ensure_sqlite_database_file() {
    [ "${DB_CONNECTION:-sqlite}" = "sqlite" ] || return 0

    local db_file="${DB_DATABASE:-/var/www/html/database/database.sqlite}"
    case "$db_file" in
        /*) ;;
        *) db_file="/var/www/html/${db_file}" ;;
    esac

    mkdir -p "$(dirname "$db_file")"
    [ -f "$db_file" ] || touch "$db_file"

    if id -u application >/dev/null 2>&1; then
        chown application:application "$(dirname "$db_file")" "$db_file" 2>/dev/null || true
    fi
    chmod 775 "$(dirname "$db_file")" 2>/dev/null || true
    chmod 664 "$db_file" 2>/dev/null || true

    log "sqlite ready at ${db_file} ($(stat -c '%U:%G %a' "$db_file" 2>/dev/null || echo '?'))"

    if [ "${APP_ENV:-local}" = "production" ]; then
        log "DIQQAT: productionda SQLite — Render konteyner diski ephemeral, har deployda ma'lumot yo'qoladi. PostgreSQL tavsiya etiladi (docs/operations/render-deploy.md)."
    fi
}

# ---- 3. Tashqi DB (Postgres/MySQL) kutish ----
wait_for_external_database() {
    local conn="${DB_CONNECTION:-sqlite}"
    [ "$conn" != "sqlite" ] || return 0

    local host="${DB_HOST:-}"
    local port="${DB_PORT:-}"

    if [ -z "$host" ] && [ -n "${DATABASE_URL:-}" ]; then
        host="$(printf '%s' "$DATABASE_URL" | sed -E 's#^[a-z]+://[^@]+@([^:/?]+).*#\1#')"
        port="$(printf '%s' "$DATABASE_URL" | sed -nE 's#^[a-z]+://[^@]+@[^:/?]+:([0-9]+).*#\1#p')"
        [ -n "$port" ] || port=5432
    fi

    [ -n "$host" ] || { log "DB host aniqlanmadi (DB_HOST/DATABASE_URL yo'q) — kutish o'tkazib yuborildi."; return 0; }
    [ -n "$port" ] || case "$conn" in pgsql) port=5432 ;; mysql|mariadb) port=3306 ;; *) port=0 ;; esac

    log "waiting for ${conn} at ${host}:${port} (max 60s)..."
    local i=0
    while [ "$i" -lt 60 ]; do
        if bash -c "exec 3<>/dev/tcp/${host}/${port}" 2>/dev/null; then
            exec 3<&- 3>&- 2>/dev/null || true
            log "DB erishildi (${host}:${port})"
            return 0
        fi
        sleep 1
        i=$((i + 1))
    done
    err "DB ${host}:${port} 60s ichida erishilmadi — migratsiya muvaffaqiyatsiz bo'lishi mumkin"
    return 0
}

# ---- 4. Eski cachelarni tozalash (image/build ichidagi path'lardan) ----
clear_stale_caches() {
    log "clearing stale caches"
    artisan optimize:clear >/dev/null 2>&1 || true
    rm -f /var/www/html/bootstrap/cache/config.php \
          /var/www/html/bootstrap/cache/routes-v7.php \
          /var/www/html/bootstrap/cache/packages.php \
          /var/www/html/bootstrap/cache/services.php \
          /var/www/html/bootstrap/cache/events.php 2>/dev/null || true
}

# ---- 5. Migratsiya ----
run_migrations() {
    if [ "${RUN_MIGRATIONS:-true}" != "true" ]; then
        log "RUN_MIGRATIONS=false — migratsiya o'tkazib yuborildi."
        return 0
    fi
    log "running migrations (--force)"
    if ! artisan migrate --force --no-interaction; then
        err "migrate muvaffaqiyatsiz. Tafsilot uchun yuqoridagi artisan chiqishini ko'ring."
        return 1
    fi
    log "migrations OK"
}

# ---- 6. Productive cache ----
warm_caches() {
    log "discovering packages + warming caches"
    artisan storage:link >/dev/null 2>&1 || true
    artisan package:discover --ansi --no-interaction || { err "package:discover muvaffaqiyatsiz"; return 1; }
    artisan config:cache  || { err "config:cache muvaffaqiyatsiz"; return 1; }
    artisan route:cache   || { err "route:cache muvaffaqiyatsiz";  return 1; }
    artisan view:cache    || { err "view:cache muvaffaqiyatsiz";   return 1; }
    artisan event:cache   || { err "event:cache muvaffaqiyatsiz";  return 1; }
    log "caches ready"
}

ensure_writable_paths
ensure_sqlite_database_file
wait_for_external_database
clear_stale_caches

if ! run_migrations; then
    err "Bootstrap tugatildi migratsiya xatosi bilan — konteyner to'xtatildi."
    exit 1
fi

if ! warm_caches; then
    err "Bootstrap tugatildi cache xatosi bilan — konteyner to'xtatildi."
    exit 1
fi

log "Laravel ready ✔"
