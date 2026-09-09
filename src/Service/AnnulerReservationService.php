<?php

declare(strict_types=1);

namespace App\Service;

use App\Exception\ReservationIntrouvableException;
use App\Repository\ReservationRepositoryInterface;

/**
 * Étape 8 — Service : annulation d'une réservation.
 *
 * Une réservation annulée ne bloque plus la salle.
 */
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
