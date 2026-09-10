<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\CreerSalleDTO;
use App\Model\Salle;


final class EloquentSalleRepository implements SalleRepositoryInterface
{
    public function lister(): array
    {
        return Salle::query()->orderBy('nom')->get()->all();
    }

    public function trouverParId(int $id): ?Salle
    {
        return Salle::query()->find($id);
    }

    public function enregistrer(CreerSalleDTO $dto): Salle
    {
        $salle = new Salle();
        $salle->nom      = $dto->nom;
        $salle->batiment = $dto->batiment;
        $salle->capacite = $dto->capacite;
        $salle->type     = $dto->type;
        $salle->active   = $dto->active;
        $salle->save();

        return $salle;
    }

    public function mettreAJour(Salle $salle): Salle
    {
        $salle->save();

        return $salle;
    }
}
