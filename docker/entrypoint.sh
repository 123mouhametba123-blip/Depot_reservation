#!/bin/sh
set -e

echo "Attente de MySQL (${DB_HOST}:${DB_PORT})..."

until php /usr/local/bin/attend_db.php "$DB_HOST" "$DB_PORT" "$DB_USERNAME" "$DB_PASSWORD" >/dev/null 2>&1; do
    sleep 2
done

echo "MySQL prêt : application des migrations..."
php database/migrer.php

echo "Insertion des données initiales..."
php database/seed.php

echo "Initialisation terminée : démarrage du serveur PHP intégré."
exec "$@"
