FROM php:8.0.3
RUN apt-get update -y && apt-get install -y openssl zip unzip git libzip-dev zip libpng-dev libpq-dev
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN docker-php-ext-install pdo zip gd pdo_pgsql
WORKDIR /app
COPY . /app
RUN composer install


CMD composer config-cache && php artisan serve --host=0.0.0.0 --port=$PORT
EXPOSE $PORT