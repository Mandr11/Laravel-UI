# Stage 1: build composer dependencies
FROM composer:2 as vendor
WORKDIR /app
COPY composer.json composer.lock /app/
RUN composer install --no-dev --no-scripts --prefer-dist --no-interaction --optimize-autoloader

# Stage 2: application image
FROM php:8.2-fpm

# system deps
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql zip mbstring exif pcntl gd

# install composer (copied from vendor stage)
COPY --from=vendor /usr/bin/composer /usr/bin/composer

# install composer dependencies (faster: copy vendor from stage)
WORKDIR /var/www/html
COPY --from=vendor /app/vendor ./vendor
COPY . /var/www/html

# permissions
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# expose and run php-fpm
EXPOSE 9000
CMD ["php-fpm"]
