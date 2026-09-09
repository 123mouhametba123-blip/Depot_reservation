<?php /** @var \App\Model\Reservation $reservation */ ?>
<h1>Détail de la réservation</h1>

<table class="table detail">
    <tr><th>Salle</th><td><?= $e($reservation->salle?->nom ?? '-') ?></td></tr>
    <tr><th>Responsable</th><td><?= $e($reservation->responsable) ?></td></tr>
    <tr><th>E-mail</th><td><?= $e($reservation->email) ?></td></tr>
    <tr><th>Motif</th><td><?= $e($reservation->motif) ?></td></tr>
    <tr><th>Début</th><td><?= $e($reservation->date_debut->format('d/m/Y H:i')) ?></td></tr>
    <tr><th>Fin</th><td><?= $e($reservation->date_fin->format('d/m/Y H:i')) ?></td></tr>
    <tr>
        <th>Statut</th>
        <td>
            <?php if ($reservation->statut === \App\Model\Reservation::STATUT_CONFIRMEE): ?>
                <span class="pastille actif">confirmée</span>
            <?php else: ?>
                <span class="pastille inactif">annulée</span>
            <?php endif; ?>
        </td>
    </tr>
</table>

<div class="actions">
    <a class="bouton secondaire" href="/reservations">Retour à la liste</a>
    <?php if ($reservation->statut === \App\Model\Reservation::STATUT_CONFIRMEE): ?>
        <form method="post" action="/reservations/<?= $reservation->id ?>/cancel" class="en-ligne"
              onsubmit="return confirm('Annuler cette réservation ?');">
            <button type="submit" class="bouton danger">Annuler la réservation</button>
        </form>
    <?php endif; ?>
</div>
