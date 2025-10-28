#!/bin/bash

echo "--- Démarrage de l'environnement de développement Laravel ---"

# 1. Attendre que la base de données soit prête
echo "Attente de la base de données PostgreSQL..."
# Utilise pg_isready (nécessite postgresql-client dans le Dockerfile.dev)
/usr/bin/timeout 10 bash -c 'until pg_isready -h db -p 5432; do sleep 1; done'

# 2. Définir les permissions
echo "Définition/Correction des permissions..."
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# 3. Lancement du serveur Laravel
echo "Lancement du serveur Laravel sur http://0.0.0.0:8000"
# 'exec' permet au script de se remplacer par la commande du serveur
exec php artisan serve --host=0.0.0.0 --port=8000
