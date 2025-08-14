#!/bin/bash

# Exit on any error
set -e

echo "Setting up Laravel application..."

# Create storage directories and set permissions
mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
chmod -R 775 storage bootstrap/cache

# Generate application key if not set
php artisan key:generate --force

# Clear any existing cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Run database migrations
php artisan migrate --force

# Cache config for production
php artisan config:cache

# Start the server
echo "Starting server on port $PORT..."
php artisan serve --host=0.0.0.0 --port=$PORT
