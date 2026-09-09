<?php

declare(strict_types=1);

namespace App\Exception;

/**
 * Étape 8 — Levée quand une salle ne peut pas recevoir une réservation
 * (inexistante, inactive, période déjà prise, durée excessive, date passée…).
 */
final class SalleIndisponibleException extends \DomainException
{
}
