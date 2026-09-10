<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\CreerReservationDTO;
use App\Model\Reservation;
use App\Model\Salle;


interface ReservationRepositoryInterface
{
    /** @return list<Reservation> */
    public function lister(): array;

    /** @return list<Reservation> réservations d'une salle donnée */
    public function listerParSalle(int $salleId): array;

    public function trouverParId(int $id): ?Reservation;


    public function trouverChevauchement(int $salleId, \DateTimeImmutable $dateDebut, \DateTimeImmutable $dateFin): ?Reservation;

    public function enregistrer(CreerReservationDTO $dto): Reservation;

    public function annuler(Reservation $reservation): Reservation;
}
