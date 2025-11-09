FROM php:8.4-cli

WORKDIR /app

# Instalace PHP rozšíření + Node + npm
RUN apt-get update && apt-get install -y \
    libzip-dev zip unzip git curl nodejs npm \
 && docker-php-ext-install zip pdo_mysql \
 && apt-get clean && rm -rf /var/lib/apt/lists/*

# Nainstaluj Composer
COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer
## Kopíruj manifesty nejdříve (cache vrstvy)
COPY composer.json composer.lock ./

# Instalace PHP závislostí (composer) - potřebujeme to před spuštěním artisan příkazů
RUN composer install --no-dev --optimize-autoloader --no-interaction || true

# Kopíruj zbytek aplikace
COPY . .

# Vytvoř .env pokud chybí a vygeneruj APP_KEY (bez selhání buildu)
RUN if [ ! -f .env ]; then cp .env.example .env; fi
RUN php artisan key:generate --ansi || true

# Instalace npm závislostí a build frontend
RUN npm ci || true
RUN npm run build || true

# Vytvoř adresář pro sqlite a soubor před migracemi
RUN mkdir -p database \
 && touch database/database.sqlite \
 && chmod -R 775 database || true

# Spusť migrace (v buildu to může být volitelné - --force aby nečekalo interaktivně)
RUN php artisan migrate --force || true

# Nastavení práv pro Laravel runtime
RUN chown -R www-data:www-data storage bootstrap/cache database \
 && chmod -R 775 storage bootstrap/cache database || true

EXPOSE 80
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=80"]
