#!/usr/bin/env bash
# exit on error
set -o errexit

# Install PHP GD extension
apt-get update
apt-get install -y php8.4-gd

# Install composer dependencies
composer install --no-dev --optimize-autoloader

# Run Laravel optimizations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage symlink
php artisan storage:link

