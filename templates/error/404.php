<?php

/**
 * Page d'erreur 404.
 *
 * @var string $contenu
 */
$contenu = <<<'HTML'
<div class="erreur-page">
    <p class="gros">404</p>
    <h1>Page introuvable</h1>
    <p>La page que vous cherchez n'existe pas ou a été déplacée.</p>
    <a class="bouton" href="/">Retour à l'accueil</a>
</div>
HTML;

$titre = 'Page introuvable';
require __DIR__ . '/../layout/base.php';