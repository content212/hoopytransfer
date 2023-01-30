FROM php:8.0.3-fpm-alpine

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN set -ex \
    && apk --no-cache add openssl zip unzip git libzip-dev zip libpng-dev postgresql-dev\
    && docker-php-ext-install pdo zip gd pdo_pgsql pdo_mysql


WORKDIR /var/www/html
