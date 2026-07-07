FROM php:8.3-fpm-alpine

LABEL maintainer="MUNDONET"

RUN apk add --no-cache curl oniguruma-dev libpng-dev libzip-dev \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring gd zip intl opcache \
    && pecl install redis \
    && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
COPY . .

RUN mkdir -p storage/framework/{cache,sessions,views} storage/logs storage/app/public bootstrap/cache public/uploads/logo \
    && chmod -R 777 storage bootstrap/cache public/uploads

COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/entrypoint.sh"]
CMD ["php-fpm"]
