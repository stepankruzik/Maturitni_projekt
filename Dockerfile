# Používáme PHP 8.2 s FPM
FROM php:8.2-fpm

# Nastavení pracovní složky
WORKDIR /var/www/html

# Instalace závislostí
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libonig-dev \
    curl \
    && docker-php-ext-install pdo_mysql mbstring zip

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Kopírování projektu do obrazu
COPY . .

# Instalace PHP závislostí
RUN composer install --no-interaction --optimize-autoloader

# Nastavení oprávnění pro storage a cache
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Expose port
EXPOSE 8000

# Spuštění Laravel serveru
CMD php artisan serve --host=0.0.0.0 --port=8000
