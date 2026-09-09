<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\View\View;

/**
 * Contrôleur de la page d'accueil (route GET /).
 *
 * Affiche de simples compteurs via les repositories — aucune requête ORM
 * ne transparaît ici.
 */
final class AccueilController
{
    public function __construct(
        private readonly SalleRepositoryInterface $salles,
        private readonly ReservationRepositoryInterface $reservations,
        private readonly View $vue,
    ) {
    }

    public function index(): string
    {
        return $this->vue->rendu('accueil.php', [
            'titre'         => 'Bienvenue',
            'nbSalles'      => count($this->salles->lister()),
            'nbReservations'=> count($this->reservations->lister()),
        ]);
    }
}
