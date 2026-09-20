FROM php:8.1-fpm-alpine

# Dependencies minimal
RUN apk add --no-cache \
    git curl unzip libzip-dev icu-dev oniguruma-dev \
    bash shadow libpng-dev libjpeg-turbo-dev freetype-dev

# Install extension PHP termasuk GD
RUN docker-php-ext-configure gd \
    --with-jpeg=/usr/include/ \
    --with-freetype=/usr/include/
RUN docker-php-ext-install \
    pdo pdo_mysql mbstring intl zip bcmath gd

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Buat user sesuai host UID/GID (untuk permission)
ARG UID=1000
ARG GID=1000
RUN groupmod -g ${GID} www-data && usermod -u ${UID} -g ${GID} www-data

WORKDIR /var/www
USER www-data
