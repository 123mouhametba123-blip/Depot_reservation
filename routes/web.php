<?php

declare(strict_types=1);

use App\Controller\AccueilController;
use App\Controller\ReservationController;
use App\Controller\SalleController;
use FastRoute\RouteCollector;

/**
 * Étape 10 — Déclaration de toutes les routes FastRoute.
 *
 * Les routes ne construisent PAS les contrôleurs : elles se contentent de
 * désigner une paire [Contrôleur::class, 'action']. Le routeur utilise le
 * conteneur pour obtenir l'instance correspondante (Application).
 */
return static function (RouteCollector $r): void {
    $r->addRoute('GET', '/', [AccueilController::class, 'index']);

    $r->addRoute('GET', '/salles', [SalleController::class, 'index']);
    $r->addRoute('GET', '/salles/create', [SalleController::class, 'create']);
    $r->addRoute('POST', '/salles', [SalleController::class, 'store']);
    $r->addRoute('GET', '/salles/{id:\d+}', [SalleController::class, 'show']);
    $r->addRoute('GET', '/salles/{id:\d+}/edit', [SalleController::class, 'edit']);
    $r->addRoute('POST', '/salles/{id:\d+}/edit', [SalleController::class, 'update']);

    $r->addRoute('GET', '/reservations', [ReservationController::class, 'index']);
    $r->addRoute('GET', '/reservations/create', [ReservationController::class, 'create']);
    $r->addRoute('POST', '/reservations', [ReservationController::class, 'store']);
    $r->addRoute('GET', '/reservations/{id:\d+}', [ReservationController::class, 'show']);
    $r->addRoute('POST', '/reservations/{id:\d+}/cancel', [ReservationController::class, 'cancel']);
};
