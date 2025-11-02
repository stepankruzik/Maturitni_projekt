# Základní image PHP s FPM
FROM php:8.2-fpm

# Nastavení pracovního adresáře
WORKDIR /var/www/html

# Instalace systémových závislostí
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    npm \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalace Composeru
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Kopírování souborů aplikace
COPY . .

# Vytvoření .env souboru, pokud neexistuje
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Instalace PHP závislostí
RUN composer install --no-interaction --optimize-autoloader --prefer-dist

# Instalace Node.js závislostí a build frontendu
RUN npm install && npm run build

# Nastavení práv pro Laravel
RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 9000

CMD ["php-fpm"]
