<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\CreerSalleDTO;
use App\Model\Salle;


interface SalleRepositoryInterface
{
    /** @return list<Salle> */
    public function lister(): array;

    public function trouverParId(int $id): ?Salle;

    public function enregistrer(CreerSalleDTO $dto): Salle;

    public function mettreAJour(Salle $salle): Salle;
}
