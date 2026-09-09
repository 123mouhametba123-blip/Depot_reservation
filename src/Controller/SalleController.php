<?php

declare(strict_types=1);

namespace App\Controller;

use App\DTO\CreerSalleDTO;
use App\Exception\SalleIndisponibleException;
use App\Model\Salle;
use App\Repository\SalleRepositoryInterface;
use App\Validator\SalleValidator;
use App\View\View;

/**
 * Étape 9 — Contrôleur des salles.
 *
 * Le contrôleur ne réalise AUCUNE requête ORM : il s'appuie sur le
 * repository (accès aux données), le validateur (contrôle des entrées) et
 * la vue (rendu HTML). Il lit les données HTTP, mais jamais $_POST brut
 * n'est transmis aux services.
 */
final class SalleController
{
    public function __construct(
        private readonly SalleRepositoryInterface $salles,
        private readonly SalleValidator $validateur,
        private readonly View $vue,
    ) {
    }

    public function index(): string
    {
        return $this->vue->rendu('salle/index.php', [
            'titre'  => 'Liste des salles',
            'salles' => $this->salles->lister(),
        ]);
    }

    public function show(int $id): string
    {
        $salle = $this->salles->trouverParId($id);
        if ($salle === null) {
            http_response_code(404);
            return $this->vue->rendu('error/404.php', ['titre' => 'Page introuvable'], null);
        }

        return $this->vue->rendu('salle/show.php', [
            'titre'         => 'Détail de la salle',
            'salle'         => $salle,
            'reservations'  => $salle->reservations()->orderByDesc('date_debut')->get(),
        ]);
    }

    public function create(): string
    {
        return $this->vue->rendu('salle/form.php', [
            'titre'      => 'Ajouter une salle',
            'salle'      => null,
            'action'     => '/salles',
            'erreurs'    => $this->flash('erreurs', []),
            'anciennes'  => $this->flash('anciennes', []),
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
            header('Location: /salles/create');
            return '';
        }

        $dto = new CreerSalleDTO(
            nom: $resultat->getDonnees()['nom'],
            batiment: $resultat->getDonnees()['batiment'],
            capacite: $resultat->getDonnees()['capacite'],
            type: $resultat->getDonnees()['type'],
            active: $resultat->getDonnees()['active'],
        );

        $this->salles->enregistrer($dto);
        $_SESSION['succes'] = 'La salle a été ajoutée.';
        http_response_code(302);
        header('Location: /salles');
        return '';
    }

    public function edit(int $id): string
    {
        $salle = $this->salles->trouverParId($id);
        if ($salle === null) {
            http_response_code(404);
            return $this->vue->rendu('error/404.php', ['titre' => 'Page introuvable'], null);
        }

        return $this->vue->rendu('salle/form.php', [
            'titre'     => 'Modifier la salle',
            'salle'     => $salle,
            'action'    => "/salles/{$id}/edit",
            'erreurs'   => $this->flash('erreurs', []),
            'anciennes' => $this->flash('anciennes', []),
        ]);
    }

    public function update(int $id): string
    {
        $salle = $this->salles->trouverParId($id);
        if ($salle === null) {
            http_response_code(404);
            return $this->vue->rendu('error/404.php', ['titre' => 'Page introuvable'], null);
        }

        $donnees = $this->donneesHttp();
        $resultat = $this->validateur->validate($donnees);

        if (!$resultat->isValid()) {
            $_SESSION['erreurs'] = $resultat->errors();
            $_SESSION['anciennes'] = $donnees;
            http_response_code(302);
            header("Location: /salles/{$id}/edit");
            return '';
        }

        $v = $resultat->getDonnees();
        $salle->nom      = $v['nom'];
        $salle->batiment = $v['batiment'];
        $salle->capacite = $v['capacite'];
        $salle->type     = $v['type'];
        $salle->active   = $v['active'];

        $this->salles->mettreAJour($salle);
        $_SESSION['succes'] = 'La salle a été modifiée.';
        http_response_code(302);
        header('Location: /salles');
        return '';
    }

    /**
     * Échappement commun des données HTTP d'une salle.
     *
     * @return array<string, mixed>
     */
    private function donneesHttp(): array
    {
        return [
            'nom'      => trim((string) ($_POST['nom'] ?? '')),
            'batiment' => trim((string) ($_POST['batiment'] ?? '')),
            'capacite' => $_POST['capacite'] ?? '',
            'type'     => trim((string) ($_POST['type'] ?? '')),
            'active'   => isset($_POST['active']),
        ];
    }

    /**
     * Lit puis efface une valeur flash de session.
     *
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
