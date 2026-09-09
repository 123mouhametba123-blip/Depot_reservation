<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Exception\ReservationIntrouvableException;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\ReservationRepositoryEnMemoire;
use Tests\Fakes\SalleRepositoryEnMemoire;

/**
 * Étape 12 — Tests unitaires du service d'annulation.
 *
 *  - une réservation inexistante lève ReservationIntrouvableException ;
 *  - une réservation existante passe à l'état « annulee ».
 */
final class AnnulerReservationServiceTest extends TestCase
{
    private SalleRepositoryEnMemoire $salles;
    private ReservationRepositoryEnMemoire $reservations;
    private AnnulerReservationService $service;

    protected function setUp(): void
    {
        $this->salles       = new SalleRepositoryEnMemoire();
        $this->reservations = new ReservationRepositoryEnMemoire();
        $this->service      = new AnnulerReservationService($this->reservations);
    }

    #[Test]
    public function echoue_si_la_reservation_n_existe_pas(): void
    {
        $this->expectException(ReservationIntrouvableException::class);
        $this->service->executer(404);
    }

    #[Test]
    public function annule_une_reservation_existante(): void
    {
        $salle = new \App\Model\Salle();
        $salle->id       = 1;
        $salle->nom      = 'Salle de test';
        $salle->batiment = 'A';
        $salle->capacite = 40;
        $salle->type     = 'cours';
        $salle->active   = true;
        $this->salles->injecter(1, $salle);

        $creation = new CreerReservationService($this->salles, $this->reservations);
        $reservation = $creation->executer(
            new \App\DTO\CreerReservationDTO(
                salleId: 1,
                responsable: 'Awa Diop',
                email: 'awa.diop@exemple.sn',
                motif: 'Soutenance de stage',
                dateDebut: new \DateTimeImmutable('+2 days 10:00:00'),
                dateFin: new \DateTimeImmutable('+2 days 11:00:00'),
            ),
        );

        $this->service->executer($reservation->id);

        $this->assertSame('annulee', $reservation->statut);
        $this->assertSame('annulee', $this->reservations->trouverParId($reservation->id)->statut);
    }
}