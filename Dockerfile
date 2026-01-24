FROM php:8.2-fpm

WORKDIR /var/www

# System dependencies (Railway / Debian trixie compatible)
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

# PHP extensions required by Laravel
RUN docker-php-ext-install \
    pdo_mysql \
    mbstring \
    zip \
    exif \
    pcntl \
    gd

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copy app source
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Laravel optimizations (safe for Railway)
RUN php artisan key:generate || true
RUN php artisan config:clear
RUN php artisan route:clear
RUN php artisan view:clear

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8000

# Railway-compatible start command
CMD php artisan serve --host=0.0.0.0 --port=$PORT