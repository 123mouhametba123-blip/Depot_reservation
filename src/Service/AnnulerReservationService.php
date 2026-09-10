<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ReservationIntrouvableException;
use App\Repository\ReservationRepositoryInterface;


final class AnnulerReservationService
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservations,
    ) {
    }

    public function executer(int $reservationId): void
    {
        $reservation = $this->reservations->trouverParId($reservationId);

        if ($reservation === null) {
            throw new ReservationIntrouvableException("La réservation demandée n'existe pas.");
        }

        $this->reservations->annuler($reservation);
    }
}
