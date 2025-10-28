#!/bin/bash

echo "--- Préparation de l'environnement de production Render ---"

# 1. Définir les permissions
chmod -R 775 /var/www/html/storage
chmod -R 775 /var/www/html/bootstrap/cache

# NETTOYAGE AGRESSIF : S'assurer qu'un ancien fichier config.php corrompu n'est pas utilisé
rm -f /var/www/html/bootstrap/cache/config.php

# 2. Caching de l'application (Lit les variables injectées par l'environnement)
echo "Nettoyage et mise en cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 3. Exécution des Migrations
echo "Exécution des migrations de base de données..."
php artisan migrate --force

# 4. Lancement du Serveur Web (pour la portabilité locale et Render)
echo "Démarrage du serveur PHP intégré sur le port Render ($PORT)..."
# Cette commande est la plus stable dans un conteneur FPM/Alpine pour servir le dossier 'public'.
exec php -S 0.0.0.0:$PORT -t /var/www/html/public
