<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\DTO\CreerReservationDTO;
use App\Exception\SalleIndisponibleException;
use App\Model\Salle;
use App\Service\CreerReservationService;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\ReservationRepositoryEnMemoire;
use Tests\Fakes\SalleRepositoryEnMemoire;

/**
 * Étape 12 — Tests unitaires du service de création de réservation
 * (OSS : sans MySQL, via des doublures en mémoire des repositories).
 *
 * Les huit situations demandées par l'énoncé :
 *   1. réservation valide ;
 *   2. salle inexistante ;
 *   3. salle inactive ;
 *   4. date de fin antérieure au début ;
 *   5. durée supérieure à quatre heures ;
 *   6. date passée ;
 *   7. conflit avec une réservation ;
 *   8. réservation voisine sans chevauchement.
 *
 * Bonus : une réservation annulée ne bloque plus la salle.
 */
final class CreerReservationServiceTest extends TestCase
{
    private SalleRepositoryEnMemoire $salles;
    private ReservationRepositoryEnMemoire $reservations;
    private CreerReservationService $service;

    protected function setUp(): void
    {
        $this->salles       = new SalleRepositoryEnMemoire();
        $this->reservations = new ReservationRepositoryEnMemoire();
        $this->service      = new CreerReservationService($this->salles, $this->reservations);
    }

    #[Test]
    public function confirme_une_reservation_valide(): void
    {
        $this->injecterSalle(id: 1, active: true);

        $reservation = $this->service->executer($this->creerDto(salleId: 1));

        $this->assertSame(1, $reservation->id);
        $this->assertSame(1, $reservation->salle_id);
        $this->assertSame('confirmee', $reservation->statut);
        $this->assertCount(1, $this->reservations->lister());
    }

    #[Test]
    public function echoue_si_la_salle_n_existe_pas(): void
    {
        $this->expectException(SalleIndisponibleException::class);
        $this->service->executer($this->creerDto(salleId: 999));
    }

    #[Test]
    public function echoue_si_la_salle_est_inactive(): void
    {
        $this->injecterSalle(id: 1, active: false);

        $this->expectException(SalleIndisponibleException::class);
        $this->service->executer($this->creerDto(salleId: 1));
    }

    #[Test]
    public function echoue_si_la_date_de_fin_est_anterieure_au_debut(): void
    {
        $this->injecterSalle(id: 1, active: true);

        $dto = $this->creerDto(
            salleId: 1,
            debut: new DateTimeImmutable('+1 day 14:00:00'),
            fin: new DateTimeImmutable('+1 day 13:00:00'),
        );

        $this->expectException(SalleIndisponibleException::class);
        $this->service->executer($dto);
    }

    #[Test]
    public function echoue_si_la_duree_depasse_quatre_heures(): void
    {
        $this->injecterSalle(id: 1, active: true);

        $dto = $this->creerDto(
            salleId: 1,
            debut: new DateTimeImmutable('+1 day 09:00:00'),
            fin: new DateTimeImmutable('+1 day 14:00:00'),
        );

        $this->expectException(SalleIndisponibleException::class);
        $this->service->executer($dto);
    }

    #[Test]
    public function echoue_si_la_date_est_passee(): void
    {
        $this->injecterSalle(id: 1, active: true);

        $dto = $this->creerDto(
            salleId: 1,
            debut: new DateTimeImmutable('-2 hours'),
            fin: new DateTimeImmutable('-1 hour'),
        );

        $this->expectException(SalleIndisponibleException::class);
        $this->service->executer($dto);
    }

    #[Test]
    public function echoue_en_cas_de_conflit_avec_une_reservation(): void
    {
        $this->injecterSalle(id: 1, active: true);
        $this->reservations->enregistrer(
            $this->creerDto(salleId: 1, debut: new DateTimeImmutable('+1 day 10:00:00'), fin: new DateTimeImmutable('+1 day 12:00:00')),
        );

        $dto = $this->creerDto(
            salleId: 1,
            debut: new DateTimeImmutable('+1 day 11:00:00'),
            fin: new DateTimeImmutable('+1 day 13:00:00'),
        );

        $this->expectException(SalleIndisponibleException::class);
        $this->service->executer($dto);
    }

    #[Test]
    public function confirme_une_reservation_voisine_sans_chevauchement(): void
    {
        $this->injecterSalle(id: 1, active: true);
        $this->reservations->enregistrer(
            $this->creerDto(salleId: 1, debut: new DateTimeImmutable('+1 day 10:00:00'), fin: new DateTimeImmutable('+1 day 12:00:00')),
        );

        // La nouvelle réservation commence exactement à la fin de l'existante.
        $dto = $this->creerDto(
            salleId: 1,
            debut: new DateTimeImmutable('+1 day 12:00:00'),
            fin: new DateTimeImmutable('+1 day 14:00:00'),
        );

        $reservation = $this->service->executer($dto);

        $this->assertSame(2, $reservation->id);
        $this->assertSame('confirmee', $reservation->statut);
    }

    #[Test]
    public function une_reservation_annulee_ne_bloque_plus_la_salle(): void
    {
        $this->injecterSalle(id: 1, active: true);

        $existante = $this->reservations->enregistrer(
            $this->creerDto(salleId: 1, debut: new DateTimeImmutable('+1 day 10:00:00'), fin: new DateTimeImmutable('+1 day 12:00:00')),
        );
        $this->reservations->annuler($existante);

        $dto = $this->creerDto(
            salleId: 1,
            debut: new DateTimeImmutable('+1 day 10:30:00'),
            fin: new DateTimeImmutable('+1 day 11:30:00'),
        );

        $reservation = $this->service->executer($dto);

        $this->assertSame('confirmee', $reservation->statut);
    }

    private function injecterSalle(int $id, bool $active): void
    {
        $salle = new Salle();
        $salle->id       = $id;
        $salle->nom      = 'Salle de test';
        $salle->batiment = 'A';
        $salle->capacite = 40;
        $salle->type     = 'cours';
        $salle->active   = $active;

        $this->salles->injecter($id, $salle);
    }

    private function creerDto(
        int $salleId,
        ?DateTimeImmutable $debut = null,
        ?DateTimeImmutable $fin = null,
        string $responsable = 'Mouhamadou Bâ',
        string $email = 'expose@exemple.sn',
        string $motif = 'Exposé de projet',
    ): CreerReservationDTO {
        return new CreerReservationDTO(
            salleId: $salleId,
            responsable: $responsable,
            email: $email,
            motif: $motif,
            dateDebut: $debut ?? new DateTimeImmutable('+1 day 10:00:00'),
            dateFin: $fin ?? new DateTimeImmutable('+1 day 11:00:00'),
        );
    }
}