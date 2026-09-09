<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Validator\ReservationValidator;
use App\Validator\SalleValidator;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Étape 12 — Tests unitaires de la validation (Respect\Validation).
 *
 * Les cinq cas demandés par l'énoncé :
 *   - une adresse électronique invalide ;
 *   - un responsable vide ;
 *   - une capacité négative ;
 *   - un type de salle inconnu ;
 *   - une date incorrecte.
 *
 * Bonus : une salle et une réservation valides sont acceptées.
 */
final class ValidationTest extends TestCase
{
    #[Test]
    public function rejette_une_adresse_electronique_invalide(): void
    {
        $resultat = (new ReservationValidator())->validate([
            'salle_id'    => 1,
            'responsable' => 'Mouhamadou Bâ',
            'email'       => 'adresse-invalide',
            'motif'       => 'Exposé de projet',
            'date_debut'  => '2026-09-15 10:00:00',
            'date_fin'    => '2026-09-15 11:00:00',
        ]);

        $this->assertFalse($resultat->isValid());
        $this->assertArrayHasKey('email', $resultat->errors());
    }

    #[Test]
    public function rejette_un_responsable_vide(): void
    {
        $resultat = (new ReservationValidator())->validate([
            'salle_id'    => 1,
            'responsable' => '',
            'email'       => 'expose@exemple.sn',
            'motif'       => 'Exposé de projet',
            'date_debut'  => '2026-09-15 10:00:00',
            'date_fin'    => '2026-09-15 11:00:00',
        ]);

        $this->assertFalse($resultat->isValid());
        $this->assertArrayHasKey('responsable', $resultat->errors());
    }

    #[Test]
    public function rejette_une_capacite_negative(): void
    {
        $resultat = (new SalleValidator())->validate([
            'nom'      => 'Salle B101',
            'batiment' => 'Bâtiment A',
            'capacite' => -5,
            'type'     => 'cours',
            'active'   => true,
        ]);

        $this->assertFalse($resultat->isValid());
        $this->assertArrayHasKey('capacite', $resultat->errors());
    }

    #[Test]
    public function rejette_un_type_de_salle_inconnu(): void
    {
        $resultat = (new SalleValidator())->validate([
            'nom'      => 'Salle B101',
            'batiment' => 'Bâtiment A',
            'capacite' => 60,
            'type'     => 'piscine',
            'active'   => true,
        ]);

        $this->assertFalse($resultat->isValid());
        $this->assertArrayHasKey('type', $resultat->errors());
    }

    #[Test]
    public function rejette_une_date_incorrecte(): void
    {
        $resultat = (new ReservationValidator())->validate([
            'salle_id'    => 1,
            'responsable' => 'Mouhamadou Bâ',
            'email'       => 'expose@exemple.sn',
            'motif'       => 'Exposé de projet',
            'date_debut'  => 'pas-une-date',
            'date_fin'    => '2026-09-15 11:00:00',
        ]);

        $this->assertFalse($resultat->isValid());
        $this->assertArrayHasKey('date_debut', $resultat->errors());
    }

    #[Test]
    public function accepte_une_salle_valide(): void
    {
        $resultat = (new SalleValidator())->validate([
            'nom'      => 'Salle B101',
            'batiment' => 'Bâtiment A',
            'capacite' => '60',
            'type'     => 'cours',
            'active'   => '1',
        ]);

        $this->assertTrue($resultat->isValid());
        $this->assertSame([], $resultat->errors());
        $this->assertSame(60, $resultat->getDonnees()['capacite']);
        $this->assertTrue($resultat->getDonnees()['active']);
    }

    #[Test]
    public function accepte_une_reservation_valide(): void
    {
        $resultat = (new ReservationValidator())->validate([
            'salle_id'    => 1,
            'responsable' => 'Mouhamadou Bâ',
            'email'       => 'expose@exemple.sn',
            'motif'       => 'Exposé de projet',
            'date_debut'  => '2026-09-15 10:00:00',
            'date_fin'    => '2026-09-15 11:00:00',
        ]);

        $this->assertTrue($resultat->isValid());
        $this->assertSame(1, $resultat->getDonnees()['salle_id']);
        $this->assertInstanceOf(\DateTimeImmutable::class, $resultat->getDonnees()['date_debut']);
    }
}