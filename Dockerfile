FROM php:8.4-cli

WORKDIR /app

# Instalace PHP rozšíření + Node + npm
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl nodejs npm \
 && docker-php-ext-install zip pdo_mysql \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# Nainstaluj Composer
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# Kopíruj všechny soubory projektu
COPY . .

# Instalace PHP závislostí (vendor složka musí být před buildem)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Instalace npm závislostí a build frontend
RUN npm ci
RUN npm run build

# Připrav .env soubor pokud chybí
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Vygeneruj APP_KEY
RUN php artisan key:generate

# Nastavení práv pro Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

EXPOSE 80
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
