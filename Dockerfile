# syntax=docker/dockerfile:1.6

# --- Composer: vendor alohida bosqich (Render: zip / tarmoq / xotira) ---
# composer:2 = Alpine; git/unzip tasvirda. apt-get ishlatilmaydi.
FROM composer:2 AS vendor
WORKDIR /app

ENV COMPOSER_MEMORY_LIMIT=-1 \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MAX_PARALLEL_HTTP=2

COPY composer.json composer.lock ./

# Avvalo dist; xato bo'lsa prefer-source (zip/OOM muammolari uchun fallback).
RUN composer install \
        --no-dev \
        --no-scripts \
        --prefer-dist \
        --no-interaction \
    || (rm -rf vendor \
        && composer install \
            --no-dev \
            --no-scripts \
            --prefer-source \
            --no-interaction)

COPY . .

RUN composer dump-autoload --optimize --no-dev --no-scripts


# --- Frontend (Vite) ---
FROM node:20-bookworm-slim AS frontend
WORKDIR /build

RUN apt-get update \
    && DEBIAN_FRONTEND=noninteractive apt-get install -y --no-install-recommends ca-certificates \
    && rm -rf /var/lib/apt/lists/*

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm run build


# --- Laravel + Nginx + PHP-FPM ---
FROM webdevops/php-nginx:8.2

ENV WEB_DOCUMENT_ROOT=/var/www/html/public \
    PHP_DISMOD=ioncube \
    fpm_php_output_buffering=4096 \
    php_display_errors=0 \
    php_log_errors=1 \
    APP_PROCESS_ROLE=app

WORKDIR /var/www/html

# Dastur kodi
COPY . /var/www/html/

# Har doim .env o'rnida xotira rezidenti env o'zgaruvchilari ishlatiladi; build artifactsiz .env qolmasin.
RUN rm -f /var/www/html/.env /var/www/html/.env.backup

# Vendor va build qilingan frontend alohida bosqichlardan
COPY --from=vendor   /app/vendor                 /var/www/html/vendor
COPY --from=frontend /build/public/build         /var/www/html/public/build

# Storage, bootstrap/cache, database kataloglari egaligi va ruxsatlari.
# Image ichida sqlite fayli ishlatilmaydi (entrypoint kerak bo'lsa yaratadi),
# lekin katalog mavjud bo'lishi shart.
RUN set -eux; \
    mkdir -p \
        /var/www/html/storage/logs \
        /var/www/html/storage/framework/sessions \
        /var/www/html/storage/framework/views \
        /var/www/html/storage/framework/cache/data \
        /var/www/html/bootstrap/cache \
        /var/www/html/database; \
    chown -R application:application \
        /var/www/html/storage \
        /var/www/html/bootstrap/cache \
        /var/www/html/database; \
    chmod -R ug+rwX \
        /var/www/html/storage \
        /var/www/html/bootstrap/cache \
        /var/www/html/database

# Laravel provisioning (migratsiya, cache, sqlite fayl) — runtime
COPY docker/provision/entrypoint.d/10-laravel.sh /opt/docker/provision/entrypoint.d/10-laravel.sh
RUN chmod +x /opt/docker/provision/entrypoint.d/10-laravel.sh

# worker/scheduler rolga CMD
COPY docker/laravel-role-cmd.sh /usr/local/bin/laravel-role-cmd
RUN chmod +x /usr/local/bin/laravel-role-cmd

# Nginx php-fpm ishga tushmaguncha kutsin (Render: 502 oldini olish).
COPY docker/supervisor.d/zz-laravel-boot-order.conf /opt/docker/etc/supervisor.d/zz-laravel-boot-order.conf
COPY docker/service.d/nginx.d/99-wait-php-fpm.sh /opt/docker/bin/service.d/nginx.d/99-wait-php-fpm.sh
RUN chmod +x /opt/docker/bin/service.d/nginx.d/99-wait-php-fpm.sh

# syslog-ng ogohlantirishini yopish (dns-cache warning).
RUN if [ -f /opt/docker/etc/syslog-ng/syslog-ng.conf ]; then \
        grep -q 'dns-cache(no)' /opt/docker/etc/syslog-ng/syslog-ng.conf \
        || sed -i '/use-dns(no);/a dns-cache(no);' /opt/docker/etc/syslog-ng/syslog-ng.conf; \
    fi

# Renderda ishlaydigan HEALTHCHECK (Docker inspect uchun foydali; Render o'zi HTTP ping qiladi).
HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=5 \
    CMD curl -fsS http://127.0.0.1/container-live.txt || exit 1

CMD ["supervisord"]
