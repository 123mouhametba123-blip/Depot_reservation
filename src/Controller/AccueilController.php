<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\View\View;


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
            'titre'          => 'Bienvenue',
            'nbSalles'       => $this->salles->compter(),
            'nbReservations' => $this->reservations->compter(),
        ]);
    }
}