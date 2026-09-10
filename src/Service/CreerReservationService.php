<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CreerReservationDTO;
use App\Exception\SalleIndisponibleException;
use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;


final class CreerReservationService
{
    private const DUREE_MAXIMALE_HEURES = 4;

    public function __construct(
        private readonly SalleRepositoryInterface $salles,
        private readonly ReservationRepositoryInterface $reservations,
    ) {
    }

    public function executer(CreerReservationDTO $dto): Reservation
    {
        $salle = $this->salles->trouverParId($dto->salleId);
        if ($salle === null) {
            throw new SalleIndisponibleException("Réservation impossible : la salle demandée n'existe pas.");
        }

        if (!$salle->active) {
            throw new SalleIndisponibleException('Cette salle ne peut pas être réservée : elle est inactive.');
        }

        if ($dto->dateDebut >= $dto->dateFin) {
            throw new SalleIndisponibleException('La date de début doit précéder la date de fin.');
        }

        $duree = $dto->dateDebut->diff($dto->dateFin);
        $dureeEnHeures = $duree->days * 24 + $duree->h + $duree->i / 60 + $duree->s / 3600;
        if ($dureeEnHeures > self::DUREE_MAXIMALE_HEURES) {
            throw new SalleIndisponibleException('Une réservation ne peut pas dépasser quatre heures.');
        }

        $maintenant = new \DateTimeImmutable('now');
        if ($dto->dateDebut <= $maintenant) {
            throw new SalleIndisponibleException('La réservation doit commencer dans le futur.');
        }

        $conflit = $this->reservations->trouverChevauchement($dto->salleId, $dto->dateDebut, $dto->dateFin);
        if ($conflit !== null) {
            throw new SalleIndisponibleException('La salle est indisponible pendant cette période.');
        }

        return $this->reservations->enregistrer($dto);
    }
}
