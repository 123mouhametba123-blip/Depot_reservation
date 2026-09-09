<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * Étape 8 — Levée quand on tente d'annuler une réservation inexistante.
 */
final class ReservationIntrouvableException extends \DomainException
{
}
