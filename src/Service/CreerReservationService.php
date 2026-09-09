<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\CreerReservationDTO;
use App\Exception\SalleIndisponibleException;
use App\Model\Reservation;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;

/**
 * Étape 8 — Service : règles métier de création d'une réservation.
 *
 * Ce service ne connaît ni $_POST, ni FastRoute, ni les vues, ni le
 * conteneur : il reçoit un DTO et retourne un modèle. Les dépendances sont
 * injectées par le constructeur.
 */
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
        // 1. La salle doit exister.
        $salle = $this->salles->trouverParId($dto->salleId);
        if ($salle === null) {
            throw new SalleIndisponibleException("Réservation impossible : la salle demandée n'existe pas.");
        }

        // 2. La salle doit être active.
        if (!$salle->active) {
            throw new SalleIndisponibleException('Cette salle ne peut pas être réservée : elle est inactive.');
        }

        // 3. La date de début doit précéder la date de fin.
        if ($dto->dateDebut >= $dto->dateFin) {
            throw new SalleIndisponibleException('La date de début doit précéder la date de fin.');
        }

        // 4. La durée ne doit pas dépasser quatre heures.
        $duree = $dto->dateDebut->diff($dto->dateFin);
        $dureeEnHeures = $duree->days * 24 + $duree->h + $duree->i / 60 + $duree->s / 3600;
        if ($dureeEnHeures > self::DUREE_MAXIMALE_HEURES) {
            throw new SalleIndisponibleException('Une réservation ne peut pas dépasser quatre heures.');
        }

        // 5. La réservation doit commencer dans le futur.
        $maintenant = new \DateTimeImmutable('now');
        if ($dto->dateDebut <= $maintenant) {
            throw new SalleIndisponibleException('La réservation doit commencer dans le futur.');
        }

        // 6. Aucune réservation confirmée ne doit chevaucher cette période.
        $conflit = $this->reservations->trouverChevauchement($dto->salleId, $dto->dateDebut, $dto->dateFin);
        if ($conflit !== null) {
            throw new SalleIndisponibleException('La salle est indisponible pendant cette période.');
        }

        // 7. Créer la réservation, 8. l'enregistrer, 9. retourner le résultat.
        return $this->reservations->enregistrer($dto);
    }
}
