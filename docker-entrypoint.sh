#!/bin/sh
set -e

DB_PATH="/var/data/database.sqlite"

mkdir -p /var/data

echo "Ensuring correct permissions..."
chown -R www-data:www-data \
  /var/www/html/storage \
  /var/www/html/bootstrap/cache \
  /var/data

chmod -R 775 \
  /var/www/html/storage \
  /var/www/html/bootstrap/cache \
  /var/data

# Persistent SQLite disk
NEW_DB=false
if [ ! -s "$DB_PATH" ]; then
  echo "Creating persistent SQLite database at $DB_PATH"
  mkdir -p /var/data
  touch "$DB_PATH"
  chown www-data:www-data "$DB_PATH"
  chmod 775 "$DB_PATH"
  NEW_DB=true
else
  echo "Using existing persistent SQLite database"
fi

# Point Laravel to persistent DB
export DB_CONNECTION=sqlite
export DB_DATABASE="$DB_PATH"

echo "Running migrations..."
php artisan migrate 

if [ "$NEW_DB" = true ]; then
  echo "New database detected. Running seeds..."
  php artisan db:seed
else
  echo "Database already exists. Skipping seeds to prevent duplicates."
fi

echo "Clearing and caching app config..."
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Starting Supervisor..."
exec supervisord -n -c /etc/supervisor/conf.d/supervisor.conf
