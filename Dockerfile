# --- Frontend (Vite) alohida bosqich: asosiy PHP tasvirida Node/apt muammolari bo‘lmasin ---
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

RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --prefer-dist --no-interaction

COPY --from=frontend /build/public/build /var/www/html/public/build

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
