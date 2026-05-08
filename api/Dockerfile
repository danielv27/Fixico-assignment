FROM php:8.4-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git unzip \
        libpq-dev libsqlite3-dev libzip-dev libonig-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mbstring pcntl pdo_pgsql pdo_sqlite zip \
    && pecl install redis \
    && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-interaction --prefer-dist --no-scripts --no-autoloader

COPY . .
RUN mkdir -p \
        bootstrap/cache \
        storage/app/public storage/app/private \
        storage/framework/cache/data storage/framework/sessions \
        storage/framework/testing storage/framework/views \
        storage/logs \
    && composer dump-autoload --optimize \
    && cp .env.example .env \
    && php artisan key:generate

EXPOSE 8000

CMD ["sh", "-c", "php artisan migrate --force && php artisan serve --host=0.0.0.0 --port=8000"]
