FROM php:8.3-fpm

# Paquets système requis : nginx + compilation des extensions PHP
# (libonig pour mbstring, libxml2 pour l'ORM, unzip pour Composer).
RUN apt-get update && apt-get install -y --no-install-recommends \
        nginx \
        libonig-dev \
        libxml2-dev \
        unzip \
    && rm -rf /var/lib/apt/lists/*

# Extensions PHP nécessaires (PDO MySQL + mbstring pour l'ORM).
RUN docker-php-ext-install pdo pdo_mysql mbstring

# Composer (binaire officiel).
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Dépendances installées avant le code source (meilleur cache de build).
COPY composer.json composer.lock ./

WORKDIR /var/www/html
RUN composer install --no-dev --no-interaction --no-progress --prefer-dist --optimize-autoloader

# Code source de l'application.
COPY . .

# Configuration Nginx : document root sur /var/www/html/public,
# scripts PHP transmis à PHP-FPM (127.0.0.1:9000).
COPY docker/nginx-default.conf /etc/nginx/sites-available/default

# Script d'initialisation : attend la base, applique migrations + seed,
# puis démarre PHP-FPM et Nginx.
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY docker/attend_db.php /usr/local/bin/attend_db.php
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
CMD ["sh", "-c", "php-fpm -D && nginx -g 'daemon off;'"]