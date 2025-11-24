FROM php:8.2-fpm

# Install dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    git \
    curl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set Working Directory
WORKDIR /var/www

# Copy source code
COPY . .

# Install dependencies (composer)
RUN composer install

# Set Permission
RUN chown -R www-data:www-data /var/www

EXPOSE 9000
CMD ["php-fpm"]
