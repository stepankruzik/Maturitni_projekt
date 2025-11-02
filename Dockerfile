# Základní image PHP s FPM
FROM php:8.2-fpm

# Nastavení pracovního adresáře
WORKDIR /var/www/html

# Instalace Node.js
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpng-dev \
    libjpeg-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    curl \
    nodejs \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Instalace npm
RUN npm install -g npm@latest

# Instalace Composeru
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Kopírování souborů aplikace
COPY . .

# Vytvoření .env souboru, pokud neexistuje
RUN if [ ! -f .env ]; then cp .env.example .env; fi

# Instalace PHP závislostí
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --no-interaction

# Kopírování zbytku aplikace
COPY . .

# Finální composer install
RUN composer dump-autoload --optimize

# Instalace Node.js závislostí a build frontendu
RUN npm ci && npm run build

# Nastavení práv pro Laravel
RUN chown -R www-data:www-data \
    storage \
    bootstrap/cache \
    public

# Generování aplikačního klíče
RUN php artisan key:generate

EXPOSE 9000

CMD ["php-fpm"]
