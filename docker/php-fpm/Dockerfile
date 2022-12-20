ARG PHP_VERSION
FROM php:${PHP_VERSION}-fpm

COPY php-setup.sh /php-setup.sh
RUN bash /php-setup.sh && rm /php-setup.sh
