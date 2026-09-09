<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\DTO\CreerReservationDTO;
use App\DTO\CreerSalleDTO;
use App\Repository\EloquentReservationRepository;
use App\Repository\EloquentSalleRepository;
use DateTimeImmutable;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Étape 12 — Tests d'intégration sur une base SQLite en mémoire.
 *
 * Montre que les repositories Eloquent fonctionnent réellement avec la
 * base : création d'une salle, relation salle ↔ réservations, recherche
 * de chevauchement et annulation d'une réservation.
 */
final class EloquentReservationTest extends TestCase
{
    private EloquentSalleRepository $salles;
    private EloquentReservationRepository $reservations;

    protected function setUp(): void
    {
        $capsule = new Capsule();

        if (extension_loaded('pdo_sqlite')) {
            // Base SQLite en mémoire : auto-suffisante et rapide.
            $capsule->addConnection([
                'driver'   => 'sqlite',
                'database' => ':memory:',
            ]);
        } elseif (extension_loaded('pdo_mysql')) {
            // Repli sur MySQL : base dédiée aux tests (variables d'environnement).
            $hote    = getenv('DB_TEST_HOST') ?: '127.0.0.1';
            $port    = getenv('DB_TEST_PORT') ?: '3306';
            $base    = getenv('DB_TEST_DATABASE') ?: 'reservation_salles_tests';
            $utilisateur = getenv('DB_TEST_USERNAME') ?: 'root';
            $motDePasse  = getenv('DB_TEST_PASSWORD') ?: '';

            // La base de tests est créée automatiquement si elle n'existe pas.
            try {
                $pdo = new \PDO("mysql:host={$hote};port={$port}", $utilisateur, $motDePasse);
                $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$base}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            } catch (\Throwable $e) {
                $this->markTestSkipped('Serveur de tests MySQL indisponible : ' . $e->getMessage());
            }

            $capsule->addConnection([
                'driver'    => 'mysql',
                'host'      => $hote,
                'port'      => $port,
                'database'  => $base,
                'username'  => $utilisateur,
                'password'  => $motDePasse,
                'charset'   => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
            ]);
        } else {
            $this->markTestSkipped('Aucun driver PDO disponible pour les tests d\'intégration (pdo_sqlite ou pdo_mysql requis).');
        }

        try {
            $capsule->setAsGlobal();
            $capsule->bootEloquent();

            Capsule::schema()->dropIfExists('reservations');
            Capsule::schema()->dropIfExists('salles');

            Capsule::schema()->create('salles', static function (Blueprint $table): void {
                $table->increments('id');
                $table->string('nom', 100);
                $table->string('batiment', 100);
                $table->integer('capacite');
                $table->string('type', 50);
                $table->boolean('active')->default(true);
                $table->timestamps();
            });

            Capsule::schema()->create('reservations', static function (Blueprint $table): void {
                $table->increments('id');
                $table->unsignedInteger('salle_id');
                $table->string('responsable', 120);
                $table->string('email', 190);
                $table->text('motif');
                $table->dateTime('date_debut');
                $table->dateTime('date_fin');
                $table->string('statut', 20)->default('confirmee');
                $table->timestamps();

                $table->foreign('salle_id')->references('id')->on('salles')->onDelete('cascade');
            });
        } catch (\Throwable $exception) {
            $this->markTestSkipped('Base de tests indisponible : ' . $exception->getMessage());
        }

        $this->salles       = new EloquentSalleRepository();
        $this->reservations = new EloquentReservationRepository();
    }

    #[Test]
    public function cree_une_salle_avec_eloquent(): void
    {
        $salle = $this->salles->enregistrer(new CreerSalleDTO('Salle B101', 'Bâtiment A', 60, 'cours', true));

        $this->assertNotNull($salle->id);
        $this->assertSame('Salle B101', $salle->nom);
        $this->assertCount(1, $this->salles->lister());
        $this->assertSame('Salle B101', $this->salles->trouverParId((int) $salle->id)?->nom);
    }

    #[Test]
    public function la_relation_salle_reservations_fonctionne(): void
    {
        $salle = $this->salles->enregistrer(new CreerSalleDTO('Salle B102', 'Bâtiment A', 30, 'informatique', true));

        $reservation = $this->reservations->enregistrer($this->reserverSalle((int) $salle->id));

        $this->assertCount(1, $salle->reservations()->get());
        $this->assertSame((int) $salle->id, (int) $reservation->salle->id);
    }

    #[Test]
    public function detecte_un_chevauchement_de_periode(): void
    {
        $salle = $this->salles->enregistrer(new CreerSalleDTO('Salle B103', 'Bâtiment B', 45, 'cours', true));
        $salleId = (int) $salle->id;

        $this->reservations->enregistrer($this->reserverSalle($salleId));

        $conflit = $this->reservations->trouverChevauchement(
            $salleId,
            new DateTimeImmutable('+1 day 10:30:00'),
            new DateTimeImmutable('+1 day 11:30:00'),
        );

        $this->assertNotNull($conflit);
        $this->assertSame($salleId, (int) $conflit->salle_id);
    }

    #[Test]
    public function annule_une_reservation(): void
    {
        $salle = $this->salles->enregistrer(new CreerSalleDTO('Salle B104', 'Bâtiment B', 25, 'reunion', true));
        $salleId = (int) $salle->id;

        $reservation = $this->reservations->enregistrer($this->reserverSalle($salleId));

        $this->reservations->annuler($reservation);

        $this->assertSame('annulee', $this->reservations->trouverParId((int) $reservation->id)?->statut);
        $this->assertNull($this->reservations->trouverChevauchement(
            $salleId,
            new DateTimeImmutable('+1 day 10:30:00'),
            new DateTimeImmutable('+1 day 11:30:00'),
        ));
    }

    #[Test]
    public function filtre_les_reservations_par_salle(): void
    {
        $une   = $this->salles->enregistrer(new CreerSalleDTO('S1', 'Bâtiment A', 40, 'cours', true));
        $autre = $this->salles->enregistrer(new CreerSalleDTO('S2', 'Bâtiment A', 40, 'cours', true));

        $this->reservations->enregistrer($this->reserverSalle((int) $une->id));
        $this->reservations->enregistrer($this->reserverSalle((int) $autre->id));

        $this->assertCount(2, $this->reservations->lister());
        $this->assertCount(1, $this->reservations->listerParSalle((int) $une->id));
    }

    private function reserverSalle(int $salleId): CreerReservationDTO
    {
        return new CreerReservationDTO(
            salleId: $salleId,
            responsable: 'Mouhamadou Bâ',
            email: 'expose@exemple.sn',
            motif: 'Exposé de projet',
            dateDebut: new DateTimeImmutable('+1 day 10:00:00'),
            dateFin: new DateTimeImmutable('+1 day 11:00:00'),
        );
    }
}