#!/bin/sh
set -e

composer install --no-interaction --prefer-dist --optimize-autoloader

if [ ! -f .env ]; then
    cp .env.example .env
    php artisan key:generate --ansi
fi

php artisan migrate --seed

exec "$@"
