<?php /** @var int $nbSalles @var int $nbReservations */ ?>
<h1>Bienvenue</h1>
<p>Application de gestion des réservations de salles universitaires.<br>
   Consultez les salles, leurs disponibilités et gérez les réservations sans doublons.</p>

<div class="tableau-bord">
    <div class="carte">
        <strong><?= $e($nbSalles) ?></strong>
        <span>salles</span>
        <a href="/salles">Voir</a>
    </div>
    <div class="carte">
        <strong><?= $e($nbReservations) ?></strong>
        <span>réservations</span>
        <a href="/reservations">Voir</a>
    </div>
    <div class="carte">
        <strong>+</strong>
        <span>nouvelle réservation</span>
        <a href="/reservations/create">Réserver</a>
    </div>
</div>
