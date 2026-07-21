FROM php:8.2-fpm-alpine
RUN apk add --no-cache zip curl php-zip php-bcmath php-redis mariadb-connector-c gcc g++ libc-dev linux-headers && docker-php-ext-install pdo_mysql
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
WORKDIR /var/www/html
