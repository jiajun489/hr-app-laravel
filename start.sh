#!/bin/bash

# Exit on any error
set -e

echo "Starting deployment..."

# Install dependencies
echo "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader --no-interaction

# Create storage directories and set permissions
echo "Setting up storage directories..."
mkdir -p storage/logs
mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

# Set permissions
chmod -R 775 storage
chmod -R 775 bootstrap/cache

# Generate application key if not set
echo "Generating application key..."
php artisan key:generate --force

# Clear any existing cache
echo "Clearing cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Run database migrations (only if database is configured)
if [ ! -z "$DATABASE_URL" ] || [ ! -z "$DB_HOST" ]; then
    echo "Running database migrations..."
    php artisan migrate --force
fi

# Cache config for production
echo "Caching configuration..."
php artisan config:cache

# Start the server
echo "Starting server on port $PORT..."
php artisan serve --host=0.0.0.0 --port=$PORT
