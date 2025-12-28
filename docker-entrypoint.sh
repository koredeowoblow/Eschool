#!/bin/sh
set -e

# Fix permissions for storage and cache
echo "Fixing permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Ensure SQLite database exists
# Ensure SQLite database exists
if [ ! -f /var/www/html/database/database.sqlite ]; then
    if [ -f /var/www/seed/database.sqlite ]; then
        echo "Initializing database from build seed..."
        cp /var/www/seed/database.sqlite /var/www/html/database/database.sqlite
    else
        echo "Creating fresh database.sqlite (Warning: Empty DB)..."
        touch /var/www/html/database/database.sqlite
    fi
    chmod 775 /var/www/html/database/database.sqlite
    chown www-data:www-data /var/www/html/database/database.sqlite
fi

# Migration and seeding are handled at build time.
# If you need to run them manually in production, use the console.

# Start Supervisor
echo "Starting Supervisor..."
exec supervisord -n -c /etc/supervisor/conf.d/supervisor.conf
