# -------------------------------------------------------------
# --- ÉTAPE 1 : BUILD_STEP (Installation des dépendances) ---
# -------------------------------------------------------------
# On utilise une image complète pour l'étape de build et l'installation de Composer
FROM php:8.3-cli AS build_step

# Installe les outils et extensions nécessaires (y compris pdo_pgsql)
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libpq-dev \
    && docker-php-ext-install pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

# Installe Composer (à partir de l'image officielle)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copie TOUT le code et installe les dépendances
WORKDIR /app
COPY . /app

# Installe les dépendances de Laravel (en mode production)
RUN composer install --no-dev --optimize-autoloader --no-interaction

# -------------------------------------------------------------
# --- ÉTAPE 2 : FINAL (Image PHP-FPM Légère pour la Production) ---
# -------------------------------------------------------------
# Image finale : php-fpm-alpine pour la stabilité et la légèreté
FROM php:8.3-fpm-alpine AS final

# Installe les dépendances minimales et extensions spécifiques
RUN apk update && apk add --no-cache \
    postgresql-dev \
    libpq \
    sed \
    && docker-php-ext-install pdo_pgsql \
    && rm -rf /var/cache/apk/*

# Définit le répertoire de travail
WORKDIR /var/www/html

# Copie le code et les dépendances depuis l'étape 'build_step'
COPY --from=build_step /app /var/www/html

# Copie le script de démarrage
COPY start.sh /usr/local/bin/start.sh

# Correction CRLF : Garantit que le script s'exécute sous Linux
RUN sed -i 's/\r$//' /usr/local/bin/start.sh

# Rendre le script exécutable
RUN chmod +x /usr/local/bin/start.sh

# Définition des permissions pour l'utilisateur www-data (standard FPM)
RUN chown -R www-data:www-data /var/www/html

# Définir le port d'écoute interne
EXPOSE 10000

# Le point d'entrée du conteneur
CMD ["/bin/sh", "/usr/local/bin/start.sh"]
