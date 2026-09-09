<?php

declare(strict_types=1);

use App\Application;
use DI\ContainerBuilder;
use Illuminate\Database\Capsule\Manager as Capsule;

/**
 * Étape 11 — Front Controller : unique point d'entrée de l'application.
 *
 * Seul ce fichier (et l'Application qu'il construit) appelle $container->get().
 * Toutes les classes métier reçoivent leurs dépendances par le constructeur.
 */
require dirname(__DIR__) . '/vendor/autoload.php';

$builder = new ContainerBuilder();
$builder->addDefinitions(dirname(__DIR__) . '/config/container.php');

$container = $builder->build();

// Démarre Eloquent : le Capsule est résolu une seule fois (connexion globale).
$container->get(Capsule::class);

$application = $container->get(Application::class);
$application->run();
