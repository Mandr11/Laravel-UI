# ----------------------------------------
# STAGE 1: Build Assets (Frontend dengan Vite)
# ----------------------------------------
FROM node:20-alpine AS node_builder

WORKDIR /var/www/html

COPY package.json package-lock.json ./
RUN npm install

# Salin semua file proyek untuk proses build
COPY . .

# Jalankan proses build frontend (seperti yang didefinisikan di package.json)
# Log Anda menunjukkan proses ini memakan waktu 23 menit, namun akan di-cache setelah build pertama berhasil.
RUN npm run build

# ----------------------------------------
# STAGE 2: Install PHP Dependencies (Composer)
# Menggunakan PHP 8.2 CLI (sesuai runtime dan kompatibel dengan composer.lock)
# ----------------------------------------
FROM php:8.2-cli-alpine AS composer_installer

# Instal dependensi sistem yang dibutuhkan Composer (git, unzip, libzip-dev, curl)
# Curl ditambahkan untuk menginstal Composer.
RUN apk update && apk add --no-cache git unzip libzip-dev curl

# Instal Composer secara global
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Atur direktori kerja di dalam container
WORKDIR /var/www/html

# Salin file Composer
COPY composer.json composer.lock ./

# ✅ PERBAIKAN TIMEOUT: Set batas waktu proses Composer ke 0 (tidak terbatas) 
# agar proses download tidak terputus karena batas waktu 300 detik.
ENV COMPOSER_PROCESS_TIMEOUT=0

# Instal dependensi PHP (hanya untuk produksi)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# ----------------------------------------
# STAGE 3: Final Application Image (PHP 8.2 FPM Runtime)
# ----------------------------------------
# Menggunakan image PHP-FPM dengan Alpine Linux untuk ukuran yang lebih kecil.
FROM php:8.2-fpm-alpine

# Instal dependensi sistem dan ekstensi PHP yang umum dibutuhkan Laravel
# Log Anda menunjukkan langkah ini sangat lama karena kompilasi, tetapi berhasil.
# Kita tambahkan lagi agar ter-cache.
RUN apk update && apk add --no-cache \
    git \
    zip \
    unzip \
    libzip-dev \
    sqlite \
    sqlite-dev \
    libpng-dev \
    oniguruma-dev \
    icu-dev \
    && docker-php-ext-install \
        pdo_mysql \
        zip \
        bcmath \
        exif \
        pcntl \
        gd \
        intl \
    && apk del --no-cache libzip-dev sqlite-dev libpng-dev oniguruma-dev

# Atur direktori kerja (sesuai standar Laravel)
WORKDIR /var/www/html

# Salin file aplikasi
COPY . .

# Salin folder vendor dari stage composer_installer (Stage 2)
COPY --from=composer_installer /usr/local/bin/composer /usr/local/bin/composer
COPY --from=composer_installer /var/www/html/vendor vendor

# Salin aset frontend yang sudah di-build dari stage node_builder (Stage 1)
COPY --from=node_builder /var/www/html/public/build public/build

# Berikan hak akses (permissions) yang benar untuk folder penyimpanan
RUN chown -R www-data:www-data /var/www/html/storage \
    && chown -R www-data:www-data /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage \
    && chmod -R 775 /var/www/html/bootstrap/cache

# Ekspos port 9000 (port FPM default)
EXPOSE 9000

# Perintah default untuk menjalankan PHP-FPM
CMD ["php-fpm"]
