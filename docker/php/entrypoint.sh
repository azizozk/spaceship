#!/bin/bash
set -e

cd /var/www/app

# Install dependencies if vendor directory is missing
if [ ! -d "vendor" ]; then
    echo "[entrypoint] Running composer install..."
    composer install --no-interaction --prefer-dist --optimize-autoloader
fi

# Run Symfony cache warmup in non-dev environments
if [ "${APP_ENV}" != "dev" ]; then
    echo "[entrypoint] Warming up Symfony cache..."
    php bin/console cache:warmup
fi

exec "$@"
