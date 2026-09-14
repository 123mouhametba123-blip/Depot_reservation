<?php

declare(strict_types=1);

/**
 * Script de migration — applique les migrations dans l'ordre alphabétique
 * depuis database/migrations/.
 *
 * Usage : php database/migrer.php
 */

use Illuminate\Database\Capsule\Manager as Capsule;

require dirname(__DIR__) . '/vendor/autoload.php';

$demarrer = require dirname(__DIR__) . '/config/database.php';
$demarrer();

$repertoire = dirname(__DIR__) . '/database/migrations';
$fichiers = glob($repertoire . '/*.php') ?: [];
sort($fichiers);

$pilote = Capsule::connection()->getDriverName();

// Suppression préalable des tables dépendantes (PostgreSQL refuse un DROP
// tant qu'une clé étrangère référence la table ; MySQL s'en charge via
// SET FOREIGN_KEY_CHECKS). Tables classées en ordre inverse de dépendance.
if ($pilote === 'pgsql') {
    Capsule::statement('DROP TABLE IF EXISTS "reservations" CASCADE');
    Capsule::statement('DROP TABLE IF EXISTS "salles" CASCADE');
} else {
    Capsule::statement('SET FOREIGN_KEY_CHECKS = 0');
}

foreach ($fichiers as $fichier) {
    $basename = basename($fichier);
    $migration = require $fichier;

    if (!is_callable($migration)) {
        throw new RuntimeException("La migration {$basename} doit retourner une fonction.");
    }

    $migration();
    echo "Migration appliquée : {$basename}\n";
}

if ($pilote !== 'pgsql') {
    Capsule::statement('SET FOREIGN_KEY_CHECKS = 1');
}

$base = $_ENV['DB_DATABASE'] ?? $_SERVER['DB_DATABASE'] ?? 'reservation_salles';
echo "Schéma de la base '{$base}' à jour.\n";
