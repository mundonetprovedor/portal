FROM php:8.3-fpm-alpine

LABEL maintainer="MUNDONET"

# Install system dependencies
RUN apk add --no-cache \
    git \
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
    nginx

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd xml zip intl opcache

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . /var/www

# Copy docker configs
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini
COPY docker/nginx/nginx.conf /etc/nginx/http.d/default.conf
COPY docker/php/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Create required directories and set permissions
RUN mkdir -p /var/www/storage/framework/{cache,sessions,views} \
    /var/www/storage/logs \
    /var/www/storage/app/public \
    /var/www/bootstrap/cache \
    /var/www/public/uploads/logo \
    /var/log/nginx \
    /var/run/nginx \
    && chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/bootstrap/cache \
    && chown -R www-data:www-data /var/log/nginx \
    && chown -R www-data:www-data /var/run/nginx

EXPOSE 80

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
