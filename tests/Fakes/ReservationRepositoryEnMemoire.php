<?php

declare(strict_types=1);

namespace Tests\Fakes;

use App\DTO\CreerReservationDTO;
use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;
use DateTimeImmutable;

/**
 * Doublure (fake) du repository des réservations utilisée par les tests
 * unitaires. Recalcule le chevauchement en mémoire sur les modèles.
 */
final class ReservationRepositoryEnMemoire implements ReservationRepositoryInterface
{
    /** @var array<int, Reservation> */
    private array $reservations = [];

    private int $prochainId = 1;

    public function lister(): array
    {
        return array_values($this->reservations);
    }

    public function listerParSalle(int $salleId): array
    {
        return array_values(array_filter(
            $this->reservations,
            static fn (Reservation $r): bool => $r->salle_id === $salleId,
        ));
    }

    public function trouverParId(int $id): ?Reservation
    {
        return $this->reservations[$id] ?? null;
    }

    public function trouverChevauchement(int $salleId, DateTimeImmutable $dateDebut, DateTimeImmutable $dateFin): ?Reservation
    {
        foreach ($this->reservations as $reservation) {
            if ((int) $reservation->salle_id !== $salleId) {
                continue;
            }
            if ($reservation->statut !== Reservation::STATUT_CONFIRMEE) {
                continue;
            }

            $debutExistant = $reservation->date_debut;
            $finExistante  = $reservation->date_fin;

            // nouveauDebut < existant.dateFin ET nouvelleFin > existant.dateDebut
            if ($dateDebut < $finExistante && $dateFin > $debutExistant) {
                return $reservation;
            }
        }

        return null;
    }

    public function enregistrer(CreerReservationDTO $dto): Reservation
    {
        $reservation = new Reservation();
        $reservation->id         = $this->prochainId++;
        $reservation->salle_id   = $dto->salleId;
        $reservation->responsable = $dto->responsable;
        $reservation->email      = $dto->email;
        $reservation->motif      = $dto->motif;
        $reservation->date_debut = $dto->dateDebut;
        $reservation->date_fin   = $dto->dateFin;
        $reservation->statut     = Reservation::STATUT_CONFIRMEE;

        $this->reservations[$reservation->id] = $reservation;

        return $reservation;
    }

    public function annuler(Reservation $reservation): Reservation
    {
        $reservation->statut = Reservation::STATUT_ANNULEE;
        $this->reservations[$reservation->id] = $reservation;

        return $reservation;
    }

    public function compter(): int
    {
        return count($this->reservations);
    }

    /** Aide de test : injecte une réservation existante préconfigurée. */
    public function injecter(int $id, Reservation $reservation): void
    {
        $this->reservations[$id] = $reservation;
    }
}
