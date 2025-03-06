FROM php:8.1-fpm

COPY php.ini /usr/local/etc/php/

RUN apt update && apt install -y \
    mariadb-client \
    zlib1g-dev libzip-dev unzip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql zip gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
