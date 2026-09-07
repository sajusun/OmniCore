#!/usr/bin/env bash
set -e

# Wait for database if configured
if [ -n "$DB_HOST" ]; then
    echo "Waiting for database connection at $DB_HOST:$DB_PORT..."
    until nc -z -v -w30 "$DB_HOST" "${DB_PORT:-3306}" 2>/dev/null; do
        echo "Database is unavailable - sleeping 2s"
        sleep 2
    done
    echo "Database is reachable!"
fi

# Ensure storage directories exist and have proper permissions
mkdir -p /var/www/html/storage/framework/{sessions,views,cache} /var/www/html/storage/logs /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Run optimizations if in production
if [ "$APP_ENV" = "production" ]; then
    echo "Optimizing application for production..."
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

exec "$@"
