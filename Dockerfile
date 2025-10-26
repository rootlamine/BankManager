# Étape 1 : Build des dépendances Composer
FROM composer:2.6 AS composer-build

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts

# Étape 2 : Image finale PHP-FPM
FROM php:8.3-fpm-alpine

RUN apk add --no-cache bash postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www/html

# Copier vendor depuis l’étape composer
COPY --from=composer-build /app/vendor ./vendor
COPY . .

# Créer dossiers storage et bootstrap/cache avec les bons droits
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Port pour dev et prod
EXPOSE 8000 9000

# Variable pour choisir mode dev/prod
ARG APP_ENV=production
ENV APP_ENV=${APP_ENV}

# Commande par défaut selon l’environnement
CMD ["sh", "-c", "if [ \"$APP_ENV\" = 'local' ]; then php artisan serve --host=0.0.0.0 --port=8000; else php-fpm; fi"]
