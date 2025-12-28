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
    nginx \
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

# Run migrations and seeds during build
# We set DB_CONNECTION=sqlite to ensure it uses the file we just created
RUN php artisan migrate --force --database=sqlite && \
    php artisan db:seed --force --database=sqlite && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache

# Set Laravel storage permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html && \
    chmod -R 775 storage bootstrap/cache

# Configure Nginx
COPY nginx.conf /etc/nginx/sites-enabled/default

# Configure Supervisor
# Configure Supervisor
COPY supervisor.conf /etc/supervisor/conf.d/supervisor.conf

# Copy Entrypoint
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Expose HTTP + Reverb WS
EXPOSE 80
EXPOSE 8080

# Start via Entrypoint
ENTRYPOINT ["docker-entrypoint.sh"]
