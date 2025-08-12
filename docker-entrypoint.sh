#!/bin/bash
set -e

echo "Starting Laravel application setup..."

# Create .env file if it doesn't exist
if [ ! -f .env ]; then
    echo "Creating .env file from .env.example"
    cp .env.example .env
fi

# Generate app key if not already set
if ! grep -q "APP_KEY=base64:" .env 2>/dev/null; then
    echo "Generating application key..."
    php artisan key:generate --force
else
    echo "App key already exists"
fi

# Clear all caches
echo "Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Run database migrations
echo "Running database migrations..."
php artisan migrate --force

# Cache configuration for production
echo "Caching configuration..."
php artisan config:cache

# Start the server
echo "Starting server on port ${PORT:-8000}..."
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
