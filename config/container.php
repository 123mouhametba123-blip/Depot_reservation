<?php

declare(strict_types=1);

use App\Application;
use App\Controller\ReservationController;
use App\Controller\SalleController;
use App\Repository\EloquentReservationRepository;
use App\Repository\EloquentSalleRepository;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use App\Validator\ReservationValidator;
use App\Validator\SalleValidator;
use App\View\View;
use FastRoute\Dispatcher;
use Illuminate\Database\Capsule\Manager as Capsule;

use function DI\autowire;
use function DI\factory;

/**
 * Étape 11 — Conteneur d'injection de dépendances (PHP-DI).
 *
 * - autowiring  : pour les classes concrètes simples (constructeur auto-résolu) ;
 * - définitions : pour les interfaces, on indique l'implémentation ;
 * - factories   : pour les objets nécessitant une configuration (Capsule, Dispatcher).
 *
 * Seul le point d'entrée (public/index.php) fait $container->get().
 */
return [

    // --- Interfaces → implémentations concrètes -------------------------
    SalleRepositoryInterface::class =>
        autowire(EloquentSalleRepository::class),

    ReservationRepositoryInterface::class =>
        autowire(EloquentReservationRepository::class),

    // --- ORM Eloquent : factory qui configure la connexion une fois ----
    Capsule::class =>
        factory(require dirname(__DIR__) . '/config/database.php'),

    // --- Routeur FastRoute : factory à partir du fichier de routes ----
    Dispatcher::class =>
        factory(static function (): Dispatcher {
            $routes = require dirname(__DIR__) . '/routes/web.php';
            return FastRoute\simpleDispatcher($routes);
        }),

    // --- Vue : répertoire des templates -------------------------------
    View::class =>
        factory(static fn (): View => new View(dirname(__DIR__) . '/templates')),

    // --- Services / validateurs : autowiring suffit -------------------
    CreerReservationService::class =>
        autowire(CreerReservationService::class),

    AnnulerReservationService::class =>
        autowire(AnnulerReservationService::class),

    SalleValidator::class =>
        autowire(SalleValidator::class),

    ReservationValidator::class =>
        autowire(ReservationValidator::class),

    // --- Contrôleurs : autowiring via le constructeur -----------------
    SalleController::class =>
        autowire(SalleController::class),

    ReservationController::class =>
        autowire(ReservationController::class),

    // --- Front controller : construit avec le conteneur seul -----------
    Application::class =>
        autowire(Application::class),

];
