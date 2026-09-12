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

// Démarre la session une seule fois pour tous les handlers (lecture ET écriture
// des messages flash dans les contrôleurs) et son cookie associé.
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

// Serveur PHP intégré : servir directement les fichiers statiques existants
// (CSS, JS, images) sans les faire passer par le routeur.
$chemin = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
if ($chemin !== '/' && is_file(__DIR__ . $chemin)) {
    return false;
}

$builder = new ContainerBuilder();
$builder->addDefinitions(dirname(__DIR__) . '/config/container.php');

$container = $builder->build();

// Démarre Eloquent : le Capsule est résolu une seule fois (connexion globale).
$container->get(Capsule::class);

$application = $container->get(Application::class);
$application->run();
