#!/bin/bash

# Exit if there are any errors
set -e

apt-get update

apt-get install -y \
    git \
    unzip \
    nano \
    libzip-dev \
    libmcrypt-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libc-client-dev \
    libkrb5-dev

docker-php-ext-configure gd --with-freetype --with-jpeg

docker-php-ext-install -j$(nproc) pdo_mysql iconv pcntl zip gd opcache

pecl install -o -f redis
docker-php-ext-enable redis
