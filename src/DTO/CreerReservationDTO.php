<?php

declare(strict_types=1);

namespace App\DTO;

/**
 * Étape 6 — Objet de transport de données (DTO) pour la création d'une
 * réservation.
 *
 * Contient des données correctement typées. Le DTO ne sait persister quoi
 * que ce soit : il n'appelle jamais save() et ne contient aucune règle
 * métier (le chevauchement est géré par le service).
 */
final class CreerReservationDTO
{
    public function __construct(
        public readonly int $salleId,
        public readonly string $responsable,
        public readonly string $email,
        public readonly string $motif,
        public readonly \DateTimeImmutable $dateDebut,
        public readonly \DateTimeImmutable $dateFin,
    ) {
    }
}
