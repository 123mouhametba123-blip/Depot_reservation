<?php

declare(strict_types=1);

namespace App;

use App\View\View;
use FastRoute\Dispatcher;
use Psr\Container\ContainerInterface;


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

        // FastRoute fournit les paramètres dynamiques sous forme de chaînes
        // (ex. {"id":"1"}). Les actions sont typées int ; on convertit donc
        // explicitement les valeurs numériques pour éviter tout TypeError.
        $parametres = array_map(
            static fn (mixed $valeur): mixed => is_numeric($valeur) ? (int) $valeur : $valeur,
            $parametres,
        );

        return (string) call_user_func_array([$controleur, $action], array_values($parametres));
    }
}
