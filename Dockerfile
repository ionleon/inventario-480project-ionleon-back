FROM php:8.3-fpm

# Instalar dependencias del sistema y drivers de Postgres
RUN apt-get update && apt-get install -y \
    libpq-dev \
    git \
    unzip \
    && docker-php-ext-install pdo pdo_pgsql

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Instalar Symfony CLI (muy útil para desarrollo)
RUN curl -sS https://get.symfony.com/cli/installer | bash \
    && mv /root/.symfony5/bin/symfony /usr/local/bin/symfony

RUN git config --global --add safe.directory /var/www/html

WORKDIR /var/www/html

RUN pecl install redis && docker-php-ext-enable redis
