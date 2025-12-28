#!/bin/sh
set -e

echo "Fixing permissions..."
chown -R www-data:www-data \
  /var/www/html/storage \
  /var/www/html/bootstrap/cache \
  /var/www/html/database

chmod -R 775 \
  /var/www/html/storage \
  /var/www/html/bootstrap/cache \
  /var/www/html/database

# Ensure SQLite DB exists
if [ ! -s /var/www/html/database/database.sqlite ]; then
  if [ -f /var/www/seed/database.sqlite ]; then
    echo "Restoring database from build seed..."
    cp /var/www/seed/database.sqlite /var/www/html/database/database.sqlite
  else
    echo "Creating empty SQLite database..."
    touch /var/www/html/database/database.sqlite
  fi

  chmod 775 /var/www/html/database/database.sqlite
  chown www-data:www-data /var/www/html/database/database.sqlite
fi

echo "Caching configuration..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting Supervisor..."
exec supervisord -n -c /etc/supervisor/conf.d/supervisor.conf
