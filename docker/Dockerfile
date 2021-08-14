FROM php:8.0-cli-alpine
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer
RUN apk add php8-zip
RUN apk add php8-dev
RUN apk add autoconf g++ make
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug
COPY .. /code
WORKDIR /code
CMD ["php", "-S", "0.0.0.0:8000", "index.php"]
