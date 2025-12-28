# Use official PHP image with FPM
FROM php:8.2-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libsqlite3-dev \
    zip \
    supervisor \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite mbstring zip bcmath pcntl

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy app files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Create SQLite database file if it doesn't exist
RUN mkdir -p database && \
    touch database/database.sqlite && \
    chmod 775 database/database.sqlite

# Set Laravel storage permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod -R 775 storage bootstrap/cache

# Build caches at runtime to avoid build-time DB errors
RUN php artisan config:clear || true && \
    php artisan route:clear || true && \
    php artisan view:clear || true

# Copy Supervisor config
COPY supervisor.conf /etc/supervisor/conf.d/supervisor.conf

# Expose HTTP + Reverb WS
EXPOSE 80
EXPOSE 8080

# Start Supervisor (manages all processes)
CMD ["supervisord", "-n"]
