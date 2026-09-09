<?php

declare(strict_types=1);

namespace App\DTO;

/**
 * Étape 6 — Objet de transport de données (DTO) pour la création d'une salle.
 *
 * Le tableau $_POST n'est jamais transmis directement aux services ; il est
 * d'abord validé puis converti en DTO typé.
 */
final class CreerSalleDTO
{
    public function __construct(
        public readonly string $nom,
        public readonly string $batiment,
        public readonly int $capacite,
        public readonly string $type,
        public readonly bool $active,
    ) {
    }
}
