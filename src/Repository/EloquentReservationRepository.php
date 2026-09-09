<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\CreerReservationDTO;
use App\Model\Reservation;
use DateTimeImmutable;

/**
 * Étape 7 — Implémentation Eloquent du repository des réservations.
 */
final class EloquentReservationRepository implements ReservationRepositoryInterface
{
    public function lister(): array
    {
        return Reservation::query()->with('salle')->orderByDesc('date_debut')->get()->all();
    }

    public function listerParSalle(int $salleId): array
    {
        return Reservation::query()
            ->where('salle_id', $salleId)
            ->orderByDesc('date_debut')
            ->get()
            ->all();
    }

    public function trouverParId(int $id): ?Reservation
    {
        return Reservation::query()->with('salle')->find($id);
    }

    public function trouverChevauchement(int $salleId, DateTimeImmutable $dateDebut, DateTimeImmutable $dateFin): ?Reservation
    {
        return Reservation::query()
            ->where('salle_id', $salleId)
            ->where('statut', Reservation::STATUT_CONFIRMEE)
            ->where('date_fin', '>', $dateDebut->format('Y-m-d H:i:s'))
            ->where('date_debut', '<', $dateFin->format('Y-m-d H:i:s'))
            ->first();
    }

    public function enregistrer(CreerReservationDTO $dto): Reservation
    {
        $reservation = new Reservation();
        $reservation->salle_id    = $dto->salleId;
        $reservation->responsable = $dto->responsable;
        $reservation->email       = $dto->email;
        $reservation->motif       = $dto->motif;
        $reservation->date_debut  = $dto->dateDebut;
        $reservation->date_fin    = $dto->dateFin;
        $reservation->statut      = Reservation::STATUT_CONFIRMEE;
        $reservation->save();

        return $reservation;
    }

    public function annuler(Reservation $reservation): Reservation
    {
        $reservation->statut = Reservation::STATUT_ANNULEE;
        $reservation->save();

        return $reservation;
    }
}
