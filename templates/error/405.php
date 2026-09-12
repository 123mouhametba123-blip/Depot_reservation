<?php

/**
 * Page d'erreur 405.
 *
 * @var string $contenu
 */
$contenu = <<<'HTML'
<div class="erreur-page">
    <p class="gros">405</p>
    <h1>Méthode non autorisée</h1>
    <p>Cette action ne peut pas être effectuée avec la méthode HTTP utilisée.</p>
    <a class="bouton" href="/">Retour à l'accueil</a>
</div>
HTML;

$titre = 'Méthode non autorisée';
require __DIR__ . '/../layout/base.php';