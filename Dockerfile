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

# Runtime role script
COPY docker/entrypoint.sh /usr/local/bin/app-entrypoint
RUN chmod +x /usr/local/bin/app-entrypoint

CMD ["/usr/local/bin/app-entrypoint"]
