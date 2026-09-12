<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\DTO\CreerSalleDTO;
use App\Model\Salle;
use App\Repository\SalleRepositoryInterface;

/**
 * Doublure (fake) du repository des salles utilisée par les tests unitaires
 * pour tester le service métier sans MySQL ni Eloquent persistant.
 */
final class SalleRepositoryEnMemoire implements SalleRepositoryInterface
{
    /** @var array<int, Salle> */
    private array $salles = [];

    private int $prochainId = 1;

    public function lister(): array
    {
        return array_values($this->salles);
    }

    public function trouverParId(int $id): ?Salle
    {
        return $this->salles[$id] ?? null;
    }

    public function enregistrer(CreerSalleDTO $dto): Salle
    {
        $salle = new Salle();
        $salle->id       = $this->prochainId++;
        $salle->nom      = $dto->nom;
        $salle->batiment = $dto->batiment;
        $salle->capacite = $dto->capacite;
        $salle->type     = $dto->type;
        $salle->active   = $dto->active;

        $this->salles[$salle->id] = $salle;

        return $salle;
    }

    public function mettreAJour(Salle $salle): Salle
    {
        $this->salles[$salle->id] = $salle;

        return $salle;
    }

    public function compter(): int
    {
        return count($this->salles);
    }

    /** Aide de test : injecte directement une salle préconfigurée. */
    public function injecter(int $id, Salle $salle): void
    {
        $this->salles[$id] = $salle;
    }
}
