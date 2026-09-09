<?php

declare(strict_types=1);

namespace App\Repository;

use App\DTO\CreerReservationDTO;
use App\Model\Reservation;
use App\Model\Salle;

/**
 * Étape 7 — Contrat d'accès aux données des réservations.
 *
 * Isolé derrière une interface afin que les services métier et les
 * contrôleurs puissent être testés sans MySQL (implémentation en mémoire).
 */
interface ReservationRepositoryInterface
{
    /** @return list<Reservation> */
    public function lister(): array;

    /** @return list<Reservation> réservations d'une salle donnée */
    public function listerParSalle(int $salleId): array;

    public function trouverParId(int $id): ?Reservation;

    /**
     * Recherche un conflit : toute réservation confirmée de la salle dont
     * l'intervalle [dateDebut, dateFin) chevauche celui proposé.
     *
     * Deux réservations se chevauchent lorsque :
     *   nouveauDebut < existant.dateFin ET nouvelleFin > existant.dateDebut
     */
    public function trouverChevauchement(int $salleId, \DateTimeImmutable $dateDebut, \DateTimeImmutable $dateFin): ?Reservation;

    public function enregistrer(CreerReservationDTO $dto): Reservation;

    public function annuler(Reservation $reservation): Reservation;
}
