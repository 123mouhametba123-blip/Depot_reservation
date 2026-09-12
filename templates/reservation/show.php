<?php

/**
 * Détail d'une réservation.
 *
 * @var \App\Model\Reservation $reservation
 */
?>
<div class="actions">
    <h1><?= htmlspecialchars($reservation->motif) ?></h1>
    <div class="card-actions">
        <?php if ($reservation->annule) : ?>
            <span class="pastille inactif">✖ annulée</span>
        <?php else : ?>
            <span class="pastille actif">✔ confirmée</span>
        <?php endif; ?>
    </div>
</div>

<div class="detail">
    <table>
        <tr><th>Salle</th><td><a href="/salles/<?= $reservation->salle->id ?>"><?= htmlspecialchars($reservation->salle->nom) ?></a> — <?= htmlspecialchars($reservation->salle->batiment) ?></td></tr>
        <tr><th>Responsable</th><td><?= htmlspecialchars($reservation->responsable) ?></td></tr>
        <tr><th>Email</th><td><?= htmlspecialchars($reservation->email) ?></td></tr>
        <tr><th>Début</th><td><?= $reservation->date_debut->format('d/m/Y H:i') ?></td></tr>
        <tr><th>Fin</th><td><?= $reservation->date_fin->format('d/m/Y H:i') ?></td></tr>
    </table>
</div>

<div class="actions">
    <a class="bouton secondaire" href="/reservations">← Retour à la liste</a>
    <?php if (!$reservation->annule) : ?>
        <form class="en-ligne" method="post" action="/reservations/<?= $reservation->id ?>/cancel" onsubmit="return confirm('Annuler cette réservation ?');">
            <button class="bouton danger" type="submit">Annuler la réservation</button>
        </form>
    <?php endif; ?>
</div>