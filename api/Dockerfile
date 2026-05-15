FROM php:8.4-cli

# mlocati/docker-php-extension-installer: handles system deps, parallel builds,
# and strips dev packages on cleanup. Industry standard for PHP Docker images.
ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN install-php-extensions mbstring pcntl pdo_pgsql pdo_sqlite zip redis

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-interaction

COPY . .
RUN composer dump-autoload --optimize

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
