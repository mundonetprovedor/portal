FROM php:8.3-fpm-alpine

LABEL maintainer="MUNDONET"

# Install dependencies em uma unica camada
RUN apk add --no-cache \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    icu-dev \
    libzip-dev \
    supervisor \
    nginx \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo_mysql mbstring gd zip intl opcache \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del --purge libpng-dev libjpeg-turbo-dev freetype-dev oniguruma-dev libxml2-dev icu-dev libzip-dev \
    && rm -rf /var/cache/apk/*

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# Copia apenas composer.json e composer.lock primeiro (cache de dependencias)
COPY composer.json composer.lock* ./
RUN composer install --no-dev --no-scripts --no-autoloader --prefer-dist 2>/dev/null || true

# Copia o resto do projeto
COPY . .

# Copia configs docker
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/nginx/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/php/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Gera autoload e otimiza
RUN composer dump-autoload --optimize --no-dev \
    && composer run-script post-autoload-dump 2>/dev/null || true

# Cria diretorios e permissoes
RUN mkdir -p /var/www/storage/framework/{cache,sessions,views} \
    /var/www/storage/logs \
    /var/www/storage/app/public \
    /var/www/bootstrap/cache \
    /var/www/public/uploads/logo \
    /var/log/nginx \
    /var/run/nginx \
    && chown -R www-data:www-data /var/www \
    && chmod -R 777 /var/www/storage \
    && chmod -R 777 /var/www/bootstrap/cache \
    && chmod -R 777 /var/www/public/uploads

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
