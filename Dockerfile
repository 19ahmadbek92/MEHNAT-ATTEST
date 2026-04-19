FROM webdevops/php-nginx:8.2

ENV WEB_DOCUMENT_ROOT=/var/www/html/public
ENV PHP_DISMOD=ioncube
WORKDIR /var/www/html

# Loyiha fayllarini nusxalash
COPY . /var/www/html/

# .env ni o'chirish (productionda muhit o'zgaruvchilari bilan beriladi)
RUN rm -f /var/www/html/.env

# PHP kutubxonalarini o'rnatish
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --prefer-dist --no-interaction

# Node.js va npm ni o'rnatish, frontendni build qilish
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm ci --no-audit \
    && npm run build \
    && rm -rf node_modules

# Storage va cache papkalariga ruxsat berish
RUN mkdir -p /var/www/html/storage/logs \
    && mkdir -p /var/www/html/storage/framework/{sessions,views,cache} \
    && mkdir -p /var/www/html/bootstrap/cache \
    && chown -R application:application /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Laravel: webdevops entrypoint avvalo /opt/docker/provision/entrypoint.d/*.sh ni ishga tushiradi, keyin CMD bo'yicha.
COPY docker/provision/entrypoint.d/10-laravel.sh /opt/docker/provision/entrypoint.d/10-laravel.sh
RUN chmod +x /opt/docker/provision/entrypoint.d/10-laravel.sh

COPY docker/laravel-role-cmd.sh /usr/local/bin/laravel-role-cmd
RUN chmod +x /usr/local/bin/laravel-role-cmd

# Nginx php-fpmd dan keyin ishga tushsin (Render birinchi HEAD so'rovida 502 bo'lmasin).
COPY docker/supervisor.d/zz-laravel-boot-order.conf /opt/docker/etc/supervisor.d/zz-laravel-boot-order.conf

# Nginx ishga tushishidan oldin PHP-FPM porti LISTEN bo'lsin (supervisor tartibi yetmasa ham).
COPY docker/service.d/nginx.d/99-wait-php-fpm.sh /opt/docker/bin/service.d/nginx.d/99-wait-php-fpm.sh
RUN chmod +x /opt/docker/bin/service.d/nginx.d/99-wait-php-fpm.sh

# syslog-ng: use-dns(no) bilan bog'liq "dns-cache forced to no" ogohlantirishini bartaraf etish.
RUN sed -i '/use-dns(no);/a dns-cache(no);' /opt/docker/etc/syslog-ng/syslog-ng.conf

# Tasvir CMD=supervisord bo'lishi kerak — aks holda nginx ishlaydi, lekin PHP-FPM (9000) yo'qoladi.
CMD ["supervisord"]
