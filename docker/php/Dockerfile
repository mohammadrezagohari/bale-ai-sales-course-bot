FROM php:8.3-fpm-alpine

RUN apk add --no-cache bash git icu-dev libzip-dev oniguruma-dev sqlite-dev \
    && docker-php-ext-install intl mbstring pdo pdo_mysql pdo_sqlite zip

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/php/php.ini /usr/local/etc/php/conf.d/course-bale-bot.ini

WORKDIR /var/www/html

CMD ["php-fpm"]
