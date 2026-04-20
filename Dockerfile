# --- Composer: vendor alohida bosqich (Render: zip / tarmoq / xotira) ---
# Rasmiy composer:2 = Alpine; git/unzip allaqachon tasvirda. apt-get ishlatilmaydi.
FROM composer:2 AS vendor
WORKDIR /app

ENV COMPOSER_MEMORY_LIMIT=-1 \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_MAX_PARALLEL_HTTP=2

COPY composer.json composer.lock ./

# Avvalo dist (tez); xato bo'lsa vendor tozalanadi va git clone (prefer-source) — zip/OOM muammolarini aylanadi.
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
RUN npm ci --no-audit

COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm run build

# --- Laravel + Nginx + PHP-FPM ---
FROM webdevops/php-nginx:8.2

ENV WEB_DOCUMENT_ROOT=/var/www/html/public
ENV PHP_DISMOD=ioncube
WORKDIR /var/www/html

COPY . /var/www/html/

RUN rm -f /var/www/html/.env

COPY --from=vendor /app/vendor /var/www/html/vendor
COPY --from=frontend /build/public/build /var/www/html/public/build

# Build vaqtida package:discover (post-autoload-dump o'rniga)
RUN cp /var/www/html/.env.example /var/www/html/.env \
    && mkdir -p /var/www/html/database \
    && touch /var/www/html/database/database.sqlite \
    && php /var/www/html/artisan key:generate --force --no-interaction \
    && php /var/www/html/artisan package:discover --ansi --no-interaction \
    && rm -f /var/www/html/.env /var/www/html/database/database.sqlite

RUN mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/framework/{sessions,views,cache} \
    && mkdir -p /var/www/html/bootstrap/cache \
    && chown -R application:application /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

COPY docker/provision/entrypoint.d/10-laravel.sh /opt/docker/provision/entrypoint.d/10-laravel.sh
RUN chmod +x /opt/docker/provision/entrypoint.d/10-laravel.sh

COPY docker/laravel-role-cmd.sh /usr/local/bin/laravel-role-cmd
RUN chmod +x /usr/local/bin/laravel-role-cmd

COPY docker/supervisor.d/zz-laravel-boot-order.conf /opt/docker/etc/supervisor.d/zz-laravel-boot-order.conf

COPY docker/service.d/nginx.d/99-wait-php-fpm.sh /opt/docker/bin/service.d/nginx.d/99-wait-php-fpm.sh
RUN chmod +x /opt/docker/bin/service.d/nginx.d/99-wait-php-fpm.sh

RUN if [ -f /opt/docker/etc/syslog-ng/syslog-ng.conf ]; then \
      grep -q 'dns-cache(no)' /opt/docker/etc/syslog-ng/syslog-ng.conf \
      || sed -i '/use-dns(no);/a dns-cache(no);' /opt/docker/etc/syslog-ng/syslog-ng.conf; \
    fi

CMD ["supervisord"]
