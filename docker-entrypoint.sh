#!/bin/sh
set -e

# Fix permissions for storage and cache
echo "Fixing permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Ensure SQLite database exists
if [ ! -f /var/www/html/database/database.sqlite ]; then
    echo "Creating database.sqlite..."
    touch /var/www/html/database/database.sqlite
    chmod 775 /var/www/html/database/database.sqlite
    chown www-data:www-data /var/www/html/database/database.sqlite
fi

# Run migrations and seeds
echo "Running migrations..."
php artisan migrate --force

echo "Seeding database..."
php artisan db:seed --force

echo "Clearing caches..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Start Supervisor
echo "Starting Supervisor..."
exec supervisord -n -c /etc/supervisor/conf.d/supervisor.conf
