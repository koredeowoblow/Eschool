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
RUN touch database/database.sqlite \
    && chmod 775 database/database.sqlite

# Set Laravel storage permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Clear caches
RUN php artisan config:clear && \
    php artisan route:clear && \
    php artisan view:clear

# Expose port 80 for Render
EXPOSE 80

# Start Laravel tasks: migrate, seed, Reverb, serve
CMD sh -c "\
  echo '[1] Caching config...' && \
  php artisan config:cache && \
  echo '✓ Config cache complete' && \
  \
  echo '[2] Running migrations...' && \
  php artisan migrate --force && \
  echo '✓ Migrations complete' && \
  \
  echo '[3] Seeding database...' && \
  php artisan db:seed --force && \
  echo '✓ Seeding complete' && \
  \
#   echo '[4] Starting Reverb...' && \
#   php artisan reverb:start & \
#   REVERB_PID=$! && \
#   sleep 3 && \
#   if ! kill -0 $REVERB_PID 2>/dev/null; then \
#       echo '✗ Reverb failed to start — stopping container'; \
#       exit 1; \
#   fi && \
#   echo '✓ Reverb started' && \
#   \
  echo '[5] Starting Laravel server...' && \
  php artisan serve --host=0.0.0.0 --port=80 \
"
