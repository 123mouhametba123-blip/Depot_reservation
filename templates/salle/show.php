<?php /** @var \App\Model\Salle $salle @var \Illuminate\Support\Collection $reservations */ ?>
<h1><?= $e($salle->nom) ?></h1>

<table class="table detail">
    <tr><th>Bâtiment</th><td><?= $e($salle->batiment) ?></td></tr>
    <tr><th>Capacité</th><td><?= $e($salle->capacite) ?> places</td></tr>
    <tr><th>Type</th><td><?= $e($salle->type) ?></td></tr>
    <tr>
        <th>État</th>
        <td>
            <?php if ($salle->active): ?>
                <span class="pastille actif">active</span>
            <?php else: ?>
                <span class="pastille inactif">inactive</span>
            <?php endif; ?>
        </td>
    </tr>
</table>

<div class="actions">
    <a class="bouton" href="/salles/<?= $salle->id ?>/edit">Modifier</a>
    <a class="bouton secondaire" href="/salles">Retour à la liste</a>
</div>

<h2>Réservations de cette salle</h2>
<?php if ($reservations->isEmpty()): ?>
    <p class="vide">Aucune réservation pour cette salle.</p>
<?php else: ?>
    <table class="table">
        <thead>
        <tr>
            <th>Responsable</th>
            <th>Motif</th>
            <th>Début</th>
            <th>Fin</th>
            <th>Statut</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($reservations as $reservation): ?>
            <tr>
                <td><?= $e($reservation->responsable) ?></td>
                <td><?= $e($reservation->motif) ?></td>
                <td><?= $e($reservation->date_debut->format('d/m/Y H:i')) ?></td>
                <td><?= $e($reservation->date_fin->format('d/m/Y H:i')) ?></td>
                <td>
                    <?php if ($reservation->statut === \App\Model\Reservation::STATUT_CONFIRMEE): ?>
                        <span class="pastille actif">confirmée</span>
                    <?php else: ?>
                        <span class="pastille inactif">annulée</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
