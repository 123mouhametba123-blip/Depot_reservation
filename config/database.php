<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Dotenv\Dotenv;

/**
 * Étape 2 — Eloquent.
 *
 * Configure une unique connexion Capsule\Manager à partir des variables
 * d'environnement, puis la rend globale et démarre Eloquent.
 *
 * Ce fichier retourne une usine (factory) qui sera appelée une seule fois
 * par le conteneur d'injection de dépendances (étape 11).
 *
 * @return callable(): Capsule
 */
return static function (): Capsule {
    $racine = dirname(__DIR__);

    // Chargement des variables d'environnement depuis .env (si absent de $_ENV).
    if (empty($_ENV['DB_DRIVER']) && empty($_SERVER['DB_DRIVER'])) {
        Dotenv::createImmutable($racine)->safeLoad();
    }

    $lire = static function (string $cle): ?string {
        return getenv($cle) ?: ($_ENV[$cle] ?? $_SERVER[$cle] ?? null);
    };

    $pilote = $lire('DB_DRIVER') ?? 'mysql';

    $configuration = [
        'driver'    => $pilote,
        'database'  => $lire('DB_DATABASE') ?? 'reservation_salles',
        'prefix'    => $lire('DB_PREFIX') ?? '',
        'charset'   => $pilote === 'pgsql' ? 'utf8' : 'utf8mb4',
        'collation' => $pilote === 'pgsql' ? 'utf8' : 'utf8mb4_unicode_ci',
        'strict'    => true,
    ];

    if ($pilote !== 'sqlite') {
        $configuration['host']     = $lire('DB_HOST') ?? '127.0.0.1';
        $configuration['port']     = $lire('DB_PORT') ?? '3306';
        $configuration['username'] = $lire('DB_USERNAME') ?? 'root';
        $configuration['password'] = $lire('DB_PASSWORD') ?? '';
    }

    $capsule = new Capsule();
    $capsule->addConnection($configuration);
    $capsule->setAsGlobal();
    $capsule->bootEloquent();

    return $capsule;
};
