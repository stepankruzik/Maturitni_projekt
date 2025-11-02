#!/bin/bash

# Exit on error
set -o errexit

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Install Node dependencies and build assets
npm install
npm run build

# Optimize Laravel
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link
php artisan storage:link

# Run migrations
php artisan migrate --force