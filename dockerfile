FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    git curl unzip libzip-dev libpng-dev libonig-dev \
    libsqlite3-dev zip supervisor nginx \
    && docker-php-ext-install pdo pdo_mysql pdo_sqlite mbstring zip bcmath pcntl

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install --no-dev --optimize-autoloader

# Create SQLite DB
RUN mkdir -p database \
    && touch database/database.sqlite \
    && chmod 775 database/database.sqlite

# Run migrations and seeds during build
# We set DB_CONNECTION=sqlite to ensure it uses the file we just created
# We use a dummy APP_KEY just for the build step (migrations don't strictly need it, but Laravel might check)
RUN php artisan migrate --force --database=sqlite && \
    php artisan db:seed --force --database=sqlite

# Cache config/routes/views
RUN php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

# Permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Copy seeded DB as baseline
RUN mkdir -p /var/www/seed \
    && cp database/database.sqlite /var/www/seed/database.sqlite

COPY nginx.conf /etc/nginx/sites-enabled/default
COPY supervisor.conf /etc/supervisor/conf.d/supervisor.conf
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80 8080

ENTRYPOINT ["docker-entrypoint.sh"]
