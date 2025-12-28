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

# Migration and seeding are handled at build time.
# If you need to run them manually in production, use the console.

# Start Supervisor
echo "Starting Supervisor..."
exec supervisord -n -c /etc/supervisor/conf.d/supervisor.conf
