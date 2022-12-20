ARG PHP_VERSION
FROM php:${PHP_VERSION}-fpm

COPY php-setup.sh /php-setup.sh
RUN bash /php-setup.sh && rm /php-setup.sh

RUN apt-get update && apt-get install -y \
    rsyslog \
    apt-transport-https \
    gnupg2 \
    wget

# Composer
COPY ./workspace/install-composer.sh /install-composer.sh
RUN bash /install-composer.sh && rm /install-composer.sh

# NodeJS
RUN curl -sL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Aliases
COPY ./workspace/.bashrc /root/.bashrc

WORKDIR /app
CMD tail -f /dev/null
