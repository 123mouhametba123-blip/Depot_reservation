<?php

/**
 * Page d'accueil (tableau de bord).
 *
 * @var int $nbSalles
 * @var int $nbReservations
 */
?>
<div class="hero">
    <h1>Bienvenue sur votre espace de réservation de salles</h1>
    <p>Ce tableau de bord vous permet de suivre en un coup d'œil l'activité de vos salles de cours.</p>
    <div class="hero-actions">
        <a class="bouton" href="/salles">Explorer les salles</a>
        <a class="bouton secondaire" href="/reservations">Voir les réservations</a>
    </div>
</div>

<div class="tableau-bord">
    <div class="carte">
        <span>Réservations planifiées</span>
        <strong><?= $nbReservations ?></strong>
        <span>confirmées ou en attente</span>
        <a href="/reservations">Gérer mes réservations</a>
    </div>
    <div class="carte">
        <span>Salles enregistrées</span>
        <strong><?= $nbSalles ?></strong>
        <span>capacité totale et types disponibles</span>
        <a href="/salles">Gérer les salles</a>
    </div>
</div>