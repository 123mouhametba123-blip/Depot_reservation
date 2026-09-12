<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreerReservationDTO;
use App\Exception\ReservationIntrouvableException;
use App\Exception\SalleIndisponibleException;
use App\Repository\ReservationRepositoryInterface;
use App\Repository\SalleRepositoryInterface;
use App\Service\AnnulerReservationService;
use App\Service\CreerReservationService;
use App\Validator\ReservationValidator;
use App\View\View;


final class ReservationController
{
    public function __construct(
        private readonly ReservationRepositoryInterface $reservations,
        private readonly SalleRepositoryInterface $salles,
        private readonly ReservationValidator $validateur,
        private readonly CreerReservationService $creerReservation,
        private readonly AnnulerReservationService $annulerReservation,
        private readonly View $vue,
    ) {
    }

    public function index(): string
    {
        $salleId = filter_input(INPUT_GET, 'salle', FILTER_VALIDATE_INT);

        $liste = $salleId !== false && $salleId !== null
            ? $this->reservations->listerParSalle($salleId)
            : $this->reservations->lister();

        return $this->vue->rendu('reservation/index.php', [
            'titre'        => 'Liste des réservations',
            'reservations' => $liste,
            'salles'       => $this->salles->lister(),
            'salleFiltre'  => $salleId,
        ]);
    }

    public function show(int $id): string
    {
        $reservation = $this->reservations->trouverParId($id);
        if ($reservation === null) {
            http_response_code(404);
            return $this->vue->rendu('error/404.php', ['titre' => 'Page introuvable'], null);
        }

        return $this->vue->rendu('reservation/show.php', [
            'titre'       => 'Détail de la réservation',
            'reservation' => $reservation,
        ]);
    }

    public function create(): string
    {
        return $this->vue->rendu('reservation/form.php', [
            'titre'       => 'Créer une réservation',
            'reservation' => null,
            'action'      => '/reservations',
            'salles'      => $this->salles->lister(),
            'erreurs'     => $this->flash('erreurs', []),
            'anciennes'   => $this->flash('anciennes', []),
        ]);
    }

    public function store(): string
    {
        $donnees = $this->donneesHttp();
        $resultat = $this->validateur->validate($donnees);

        if (!$resultat->isValid()) {
            $_SESSION['erreurs'] = $resultat->errors();
            $_SESSION['anciennes'] = $donnees;
            http_response_code(302);
            header('Location: /reservations/create');
            return '';
        }

        $acceptees = $resultat->getDonnees();
        $dto = new CreerReservationDTO(
            salleId: $acceptees['salle_id'],
            responsable: $acceptees['responsable'],
            email: $acceptees['email'],
            motif: $acceptees['motif'],
            dateDebut: $acceptees['date_debut'],
            dateFin: $acceptees['date_fin'],
        );

        try {
            $reservation = $this->creerReservation->executer($dto);
        } catch (SalleIndisponibleException $e) {
            $_SESSION['erreurs'] = ['general' => [$e->getMessage()]];
            $_SESSION['anciennes'] = $donnees;
            http_response_code(302);
            header('Location: /reservations/create');
            return '';
        }

        $_SESSION['succes'] = "Réservation confirmée (n° {$reservation->id}).";
        http_response_code(302);
        header('Location: /reservations');
        return '';
    }

    public function cancel(int $id): string
    {
        try {
            $this->annulerReservation->executer($id);
        } catch (ReservationIntrouvableException $e) {
            http_response_code(404);
            return $this->vue->rendu('error/404.php', ['titre' => 'Page introuvable'], null);
        }

        $_SESSION['succes'] = 'La réservation a été annulée.';
        http_response_code(302);
        header('Location: /reservations');
        return '';
    }

    /**
     * @return array<string, mixed>
     */
    private function donneesHttp(): array
    {
        return [
            'salle_id'    => $_POST['salle_id'] ?? '',
            'responsable' => trim((string) ($_POST['responsable'] ?? '')),
            'email'       => trim((string) ($_POST['email'] ?? '')),
            'motif'       => trim((string) ($_POST['motif'] ?? '')),
            'date_debut'  => trim((string) ($_POST['date_debut'] ?? '')),
            'date_fin'    => trim((string) ($_POST['date_fin'] ?? '')),
        ];
    }

    /**
     * @template T
     * @param T $defaut
     * @return mixed|T
     */
    private function flash(string $cle, mixed $defaut = null): mixed
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }
        $valeur = $_SESSION[$cle] ?? $defaut;
        unset($_SESSION[$cle]);
        return $valeur;
    }
}
