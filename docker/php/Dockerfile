FROM php:8.1-fpm

COPY php.ini /usr/local/etc/php/

# 基本パッケージと PHP 拡張のインストール
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
      mariadb-client \
      zlib1g-dev libzip-dev unzip \
      libpng-dev libjpeg-dev libfreetype6-dev \
      # Headless Chrome 用
      chromium \
      chromium-driver \
      fonts-ipafont-gothic && \
    docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install -j$(nproc) pdo_mysql zip gd && \
    rm -rf /var/lib/apt/lists/*

# Composer バイナリをコピー
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www