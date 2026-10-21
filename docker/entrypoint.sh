#!/bin/sh
set -e

# Copy environment file if not present
if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env
fi

cd /var/www/html

# Clear any stale bootstrap caches (e.g. from host volume mount)
rm -f /var/www/html/bootstrap/cache/*.php

# Wait for MySQL if configured
if [ "$DB_CONNECTION" = "mysql" ]; then
    echo "Waiting for MySQL database connection..."
    while ! nc -z "${DB_HOST:-db}" "${DB_PORT:-3306}"; do
        sleep 1
    done
    echo "MySQL is ready!"
fi

# Generate application key if missing
php artisan key:generate --force --no-interaction

# Run migrations
php artisan migrate --force --no-interaction

# Setup Passport keys and personal access client
php artisan passport:keys --force --no-interaction
php artisan passport:client --personal --name="Challenge Personal Access Client" --no-interaction 2>/dev/null || true

# Seed default test user (admin@challenge.com / password123)
php artisan db:seed --force --no-interaction 2>/dev/null || true

echo "Application is initialized and ready!"

exec "$@"
