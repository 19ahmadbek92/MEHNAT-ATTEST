FROM webdevops/php-nginx:8.2

ENV WEB_DOCUMENT_ROOT=/var/www/html/public
WORKDIR /var/www/html

COPY . /var/www/html/

# Tasodifiy .env ni imagedan chiqarish (productionda muhit o'zgaruvchilari bilan beriladi)
RUN rm -f /var/www/html/.env

# PHP kutubxonalarini o'rnatish
RUN COMPOSER_MEMORY_LIMIT=-1 composer install --no-dev --optimize-autoloader --prefer-dist --no-interaction

# Node.js va npm ni o'rnatish
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install \
    && npm run build

# Papkalarga ruxsat berish
RUN chown -R application:application /var/www/html/storage /var/www/html/bootstrap/cache

# Ishlab chiqarish: migratsiya + kesh (xavfsiz va tez ishga tushish)
RUN echo '#!/bin/bash\n\
set -e\n\
php artisan migrate --force\n\
php artisan config:cache\n\
php artisan route:cache\n\
php artisan view:cache\n\
exec /entrypoint supervisor' > /start.sh \
    && chmod +x /start.sh

CMD ["/start.sh"]
