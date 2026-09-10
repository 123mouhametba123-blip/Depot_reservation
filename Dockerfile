FROM php:8.3-cli


RUN apt-get update && apt-get install -y --no-install-recommends \
        libonig-dev \
        libxml2-dev \
        unzip \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo pdo_mysql mbstring

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --prefer-dist --optimize-autoloader

COPY . .


COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
COPY docker/attend_db.php /usr/local/bin/attend_db.php
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
CMD ["php", "-S", "0.0.0.0:80", "-t", "public", "public/index.php"]