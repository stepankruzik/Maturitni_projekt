# 1. Vyber PHP image s Composerem
FROM php:8.2-fpm

# 2. Instalace systémových závislostí a rozšíření
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# 3. Instalace Composeru
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 4. Nastavení pracovního adresáře
WORKDIR /var/www/html

# 5. Kopírování projektu
COPY . .

# 6. Instalace PHP závislostí
RUN composer install --no-interaction --optimize-autoloader

# 7. Povolení storage a cache
RUN php artisan key:generate \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# 8. Expose port pro web server
EXPOSE 8000

# 9. Start Laravel serveru
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
