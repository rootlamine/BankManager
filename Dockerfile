# Étape 1 : Build des dépendances PHP
FROM composer:2.6 AS composer-build

WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist --no-scripts

# Étape 2 : Image finale de Laravel
FROM php:8.3-fpm-alpine

# Installer extensions nécessaires
RUN apk add --no-cache bash postgresql-dev \
    && docker-php-ext-install pdo pdo_pgsql

WORKDIR /var/www/html

# Copier vendor depuis la première étape
COPY --from=composer-build /app/vendor ./vendor
# Copier le reste du projet
COPY . .

# Donner les bons droits
RUN mkdir -p storage bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Exposer le port interne de Laravel
EXPOSE 8000

# Lancer l'application
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
