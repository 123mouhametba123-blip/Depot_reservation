#!/bin/sh
set -e

echo "Attente de la base ${DB_DRIVER:-mysql} (${DB_HOST}:${DB_PORT})..."

if [ -z "${DB_HOST}" ]; then
    echo "ERREUR : la variable DB_HOST n'est pas définie. Configurez les variables DB_* du service Render." >&2
    exit 1
fi

LIMITE=30
COMPTEUR=0

while [ "$COMPTEUR" -lt "$LIMITE" ]; do
    if php /usr/local/bin/attend_db.php "$DB_DRIVER" "$DB_HOST" "$DB_PORT" "$DB_DATABASE" "$DB_USERNAME" "$DB_PASSWORD" >/dev/null 2>&1; then
        break
    fi
    COMPTEUR=$((COMPTEUR + 1))
    sleep 2
done

if [ "$COMPTEUR" -ge "$LIMITE" ]; then
    echo "ERREUR : base injoignable après 60 s. Vérifiez les variables DB_DRIVER, DB_HOST, DB_PORT, DB_DATABASE, DB_USERNAME, DB_PASSWORD." >&2
    exit 1
fi

echo "Base prête : application des migrations..."
php database/migrer.php

echo "Insertion des données initiales..."
php database/seed.php

echo "Initialisation terminée : démarrage du serveur PHP intégré."
exec "$@"
