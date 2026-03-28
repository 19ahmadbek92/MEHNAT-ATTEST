FROM webdevops/php-nginx:8.2

# Nginx uchun asosiy papkani belgilash (Laravel uchun public qismi)
ENV WEB_DOCUMENT_ROOT=/app/public

WORKDIR /app
COPY . /app/

# PHP kutubxonalarini o'rnatish
RUN composer install --no-dev --optimize-autoloader

# Node.js va npm ni o'rnatish (Dizayn va frontend uchun)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && npm install \
    && npm run build

# Xavfsizlik va ruxsatlar
RUN chown -R application:application /app/storage /app/bootstrap/cache
