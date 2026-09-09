<?php

declare(strict_types=1);

/**
 * Bootstrap des tests : charge l'autoload Composer et initialise une
 * connexion Eloquent par défaut (SQLite :memory:). Les tests unitaires
 * s'en servent uniquement pour les conversions de types (casts datetime),
 * sans jamais exécuter la moindre requête contre une base.
 */

require dirname(__DIR__) . '/vendor/autoload.php';

$capsule = new Illuminate\Database\Capsule\Manager();
$capsule->addConnection([
    'driver'   => 'sqlite',
    'database' => ':memory:',
]);
$capsule->setAsGlobal();
$capsule->bootEloquent();