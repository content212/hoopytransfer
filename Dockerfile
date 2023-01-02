FROM php:8.0-fpm-alpine

RUN docker-php-ext-install pdo pdo_mysql sockets
RUN curl -sS https://getcomposer.org/installer​ | php -- \
    --install-dir=/usr/local/bin --filename=composer
RUN apk add --no-cache \
    libzip-dev \
    zip \
    libpng \
    libpng-dev\
    && docker-php-ext-install zip \
    && docker-php-ext-install gd 


COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .
RUN composer install
RUN php artisan serve --host=0.0.0.0