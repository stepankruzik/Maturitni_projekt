# ===== Stage 1: Build frontend =====
FROM node:20 AS frontend

WORKDIR /app

# Kopíruj package.json a package-lock.json
COPY package*.json ./

# Instalace npm závislostí
RUN npm ci

# Kopíruj zbytek frontend souborů
COPY . .

# Build frontend (Vite/Tailwind)
RUN npm run build

# ===== Stage 2: PHP backend =====
FROM php:8.4-cli

WORKDIR /var/www/html

# Instalace potřebných PHP rozšíření
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl \
 && docker-php-ext-install zip pdo_mysql

# Nainstaluj Composer
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

# Kopíruj backend + build frontend
COPY --from=frontend /app /var/www/html

# Instalace PHP závislostí
RUN composer install --no-dev --optimize-autoloader --no-interaction

# Připrav .env soubor pokud chybí
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Vygeneruj APP_KEY
RUN php artisan key:generate

# Nastavení práv pro Laravel
RUN chown -R www-data:www-data storage bootstrap/cache \
 && chmod -R 775 storage bootstrap/cache

# Expose HTTP port pro Render
EXPOSE 80

# Spuštění Laravel přes artisan serve
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
