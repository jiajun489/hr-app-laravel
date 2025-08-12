FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip \
    nodejs \
    npm

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions including PostgreSQL
RUN docker-php-ext-install pdo_pgsql pgsql mbstring exif pcntl bcmath gd

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy existing application directory contents
COPY . /var/www

# Copy existing application directory permissions
COPY --chown=www-data:www-data . /var/www

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Install Node dependencies and build assets
RUN npm install && npm run build

# Create storage directories and set permissions
RUN mkdir -p storage/logs storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache

# Create startup script
RUN echo '#!/bin/bash\n\
set -e\n\
echo "Setting up Laravel application..."\n\
php artisan key:generate --force\n\
php artisan config:clear\n\
php artisan route:clear\n\
php artisan view:clear\n\
php artisan cache:clear\n\
php artisan migrate --force\n\
php artisan config:cache\n\
echo "Starting server on port $PORT..."\n\
php artisan serve --host=0.0.0.0 --port=$PORT' > /var/www/docker-start.sh

RUN chmod +x /var/www/docker-start.sh

# Change current user to www
USER www-data

# Expose port 8000
EXPOSE 8000

# Use the startup script
CMD ["/var/www/docker-start.sh"]
