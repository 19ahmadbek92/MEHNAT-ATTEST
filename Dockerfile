FROM webdevops/php-nginx:8.2

ENV WEB_DOCUMENT_ROOT=/var/www/html/public
WORKDIR /var/www/html

COPY . /var/www/html/

# PHP kutubxonalarini o'rnatish
RUN composer install --no-dev --optimize-autoloader

# Node.js va npm ni o'rnatish
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install \
    && npm run build

# Papkalarga ruxsat berish
RUN chown -R application:application /var/www/html/storage /var/www/html/bootstrap/cache

# Saytni ishga tushirishdan oldin avtomatik bazani yangilash scripti
RUN echo '#!/bin/bash\n\
php artisan config:clear\n\
php artisan route:clear\n\
php artisan view:clear\n\
php artisan migrate --force\n\
/entrypoint supervisor' > /start.sh \
    && chmod +x /start.sh

CMD ["/start.sh"]
