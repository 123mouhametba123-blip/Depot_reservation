<?php

declare(strict_types=1);

namespace App\View;

/**
 * MVC — Vue. Rend un template PHP dans une chaîne HTML, puis l'enchâsse
 * éventuellement dans le gabarit général (layout).
 *
 * Les vues ne contiennent aucune règle métier et n'appellent ni Eloquent
 * ni le conteneur : elles reçoivent uniquement des données prêtes à afficher.
 */
final class View
{
    public function __construct(
        private readonly string $cheminBase,
    ) {
    }

    /**
     * @param array<string, mixed> $donnees
     */
    public function rendu(string $template, array $donnees = [], ?string $gabarit = 'layout/base.php'): string
    {
        $extraire = function (string $chemin, array $donneesTemplate): string {
            $e = static fn (mixed $valeur): string => htmlspecialchars((string) $valeur, ENT_QUOTES, 'UTF-8');
            extract($donneesTemplate, EXTR_SKIP);
            unset($donneesTemplate);
            ob_start();
            require $chemin;
            return (string) ob_get_clean();
        };

        $contenu = $extraire($this->resoudreChemin($template), $donnees);

        if ($gabarit === null) {
            return $contenu;
        }

        return $extraire($this->resoudreChemin($gabarit), [
            'titre'   => $donnees['titre'] ?? 'Réservation de salles',
            'contenu' => $contenu,
        ]);
    }

    private function resoudreChemin(string $template): string
    {
        $chemin = rtrim($this->cheminBase, '/') . '/' . ltrim($template, '/');
        if (!is_file($chemin)) {
            $chemin = rtrim($this->cheminBase, '/') . '/error/404.php';
        }

        return $chemin;
    }
}
