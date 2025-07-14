FROM php:8.2-fpm-alpine

RUN apk add --no-cache bash zip unzip curl \
    libpng-dev libjpeg-turbo-dev libwebp-dev libxpm-dev \
    oniguruma-dev libzip-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader \
    && chown -R www-data:www-data /var/www

CMD ["php-fpm"]
