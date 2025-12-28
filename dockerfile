# Use official PHP image with FPM
FROM php:8.2-fpm

# Install system dependencies and PHP extensions
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    sqlite3 \
    libsqlite3-dev \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite zip bcmath pcntl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy all project files
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Set file permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Expose port
EXPOSE 80

# Run deployment and start services
CMD sh -c "\
  echo '[1] Caching config...' && \
  php artisan config:cache && \
  echo '✓ Config cache complete — running already' && \
  \
  echo '[2] Running migrations...' && \
  php artisan migrate --force && \
  echo '✓ Migrations complete — running already' && \
  \
  echo '[3] Seeding database...' && \
  php artisan db:seed --force && \
  echo '✓ Seeding complete — running already' && \
  \
  echo '[4] Starting Reverb...' && \
  php artisan reverb:start & \
  REVERB_PID=$! && \
  sleep 3 && \
  if ! kill -0 $REVERB_PID 2>/dev/null; then \
      echo '✗ Reverb failed to start — stopping container'; \
      exit 1; \
  fi && \
  echo '✓ Reverb started — running already' && \
  \
  echo '[5] Starting Laravel server...' && \
  php artisan serve --host=0.0.0.0 --port=80 && \
  echo '✓ Laravel server running already' \
"
