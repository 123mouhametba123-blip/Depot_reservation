<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\CreerSalleDTO;
use App\Model\Salle;

/**
 * Étape 7 — Contrat d'accès aux données des salles.
 *
 * Les contrôleurs ne dépendent que de cette interface, jamais de
 * l'implémentation Eloquent. Aucune requête ORM ne doit transparaître
 * dans la couche contrôleur.
 */
interface SalleRepositoryInterface
{
    /** @return list<Salle> */
    public function lister(): array;

    public function trouverParId(int $id): ?Salle;

    public function enregistrer(CreerSalleDTO $dto): Salle;

    public function mettreAJour(Salle $salle): Salle;
}
