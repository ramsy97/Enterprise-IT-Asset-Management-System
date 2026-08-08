# ---- Build stage: compile frontend assets with Node ----
FROM node:22-alpine AS assets

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY resources/ ./resources/
COPY vite.config.js postcss.config.js tailwind.config.js ./
RUN npm run build

# ---- Runtime stage: PHP + Apache ----
FROM php:8.2-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
        libpng-dev libjpeg-dev libfreetype6-dev \
        libzip-dev libicu-dev libxml2-dev libpq-dev \
        curl unzip git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo pdo_mysql pdo_pgsql gd zip intl bcmath mbstring exif xml \
    && a2enmod rewrite headers \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

COPY --from=assets /app/public/build public/build

RUN composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader \
    && mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views \
               storage/logs storage/app/public/qrcodes \
    && chown -R www-data:www-data storage bootstrap/cache \
    && php artisan storage:link

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 10000

ENTRYPOINT ["bash", "/usr/local/bin/entrypoint.sh"]
