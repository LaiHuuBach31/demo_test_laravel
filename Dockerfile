FROM php:8.2-fpm-alpine

RUN apk add --no-cache bash zip unzip curl \
    libpng-dev libjpeg-turbo-dev libwebp-dev libxpm-dev \
    oniguruma-dev libzip-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Create a non-root user and group
RUN addgroup -g 1000 laravelgroup && \
    adduser -D -u 1000 -G laravelgroup laraveluser

WORKDIR /var/www

COPY . .

RUN composer install --no-dev --optimize-autoloader

# Set permissions for the user
RUN chown -R laraveluser:laravelgroup /var/www
# set permissions for storage and bootstrap/cache directories and index.php in public directory
RUN mkdir -p storage bootstrap/cache && \
    chown -R laraveluser:laravelgroup storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache
RUN chown laraveluser:laravelgroup public/index.php && \
    chmod 644 public/index.php

USER laraveluser

CMD ["php-fpm"]