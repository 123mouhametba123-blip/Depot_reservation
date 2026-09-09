<?php

declare(strict_types=1);

namespace App;

use App\View\View;
use FastRoute\Dispatcher;
use Psr\Container\ContainerInterface;

/**
 * MVC + Front Controller + Router — Orchestrateur principal.
 *
 * Point d'entrée unique de l'application puisqu'il est instancié et démarré
 * par public/index.php. Il :
 *   1. lit la requête HTTP ;
 *   2. retire la query string ;
 *   3. demande à FastRoute de la router ;
 *   4. traite les résultats (FOUND / NOT_FOUND / METHOD_NOT_ALLOWED).
 *
 * C'est ici (et dans index.php) que le conteneur est utilisé pour résoudre
 * les contrôleurs désignés par les routes — cas autorisé car c'est le point
 * d'entrée, et non une classe métier qui chercherait ses propres dépendances.
 */
final class Application
{
    public function __construct(
        private readonly Dispatcher $routeur,
        private readonly ContainerInterface $conteneur,
        private readonly View $vue,
    ) {
    }

    public function run(): void
    {
        $methode = $_SERVER['REQUEST_METHOD'] ?? 'GET';
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        $uri = rawurldecode($uri);
        $position = strpos($uri, '?');
        if ($position !== false) {
            $uri = substr($uri, 0, $position);
        }
        $uri = rtrim($uri, '/') ?: '/';

        $info = $this->routeur->dispatch($methode, $uri);

        switch ($info[0]) {
            case Dispatcher::NOT_FOUND:
                http_response_code(404);
                echo $this->vue->rendu('error/404.php', ['titre' => 'Page introuvable'], null);
                return;

            case Dispatcher::METHOD_NOT_ALLOWED:
                $autorisees = $info[1];
                http_response_code(405);
                header('Allow: ' . implode(', ', $autorisees));
                echo $this->vue->rendu('error/405.php', ['titre' => 'Méthode non autorisée'], null);
                return;

            case Dispatcher::FOUND:
                $gestionnaire = $info[1];
                $parametres   = $info[2];
                echo $this->executer($gestionnaire, $parametres);
                return;
        }
    }

    /**
     * @param array{0: class-string, 1: string} $gestionnaire [Contrôleur::class, 'action']
     * @param array<string, mixed>              $parametres variables dynamiques de la route
     */
    private function executer(array $gestionnaire, array $parametres): string
    {
        [$classeControleur, $action] = $gestionnaire;

        $controleur = $this->conteneur->get($classeControleur);

        if (!is_callable([$controleur, $action])) {
            throw new \RuntimeException("Action introuvable : {$classeControleur}::{$action}.");
        }

        return (string) call_user_func_array([$controleur, $action], array_values($parametres));
    }
}
