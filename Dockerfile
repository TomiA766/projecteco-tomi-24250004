FROM php:8.2-fpm

# RAILWAY FIX v2 — NO MYSQL CLIENT
WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    zip \
    exif \
    pcntl \
    gd

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN php artisan key:generate || true
RUN php artisan config:clear
RUN php artisan route:clear
RUN php artisan view:clear

RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=$PORT