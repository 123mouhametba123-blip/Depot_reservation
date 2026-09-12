<?php

/**
 * Liste des réservations.
 *
 * @var list<\App\Model\Reservation> $reservations
 * @var list<\App\Model\Salle> $salles
 * @var int|null $salleFiltre
 */
?>
<div class="actions">
    <h1>Liste des réservations</h1>
    <a class="bouton" href="/reservations/create">+ Nouvelle réservation</a>
</div>

<form class="filtre" method="get" action="/reservations">
    <label for="salle">Salle</label>
    <select name="salle" id="salle">
        <option value="">Toutes les salles</option>
        <?php foreach ($salles as $salle) : ?>
            <option value="<?= $salle->id ?>" <?= $salle->id === $salleFiltre ? 'selected' : '' ?>>
                <?= htmlspecialchars($salle->nom) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button class="bouton" type="submit">Filtrer</button>
</form>

<?php if ($reservations === []) : ?>
    <p class="vide">Aucune réservation pour le moment.</p>
<?php else : ?>
    <table class="table">
        <thead>
            <tr>
                <th>Motif</th>
                <th>Salle</th>
                <th>Responsable</th>
                <th>Début</th>
                <th>Fin</th>
                <th>Statut</th>
                <th class="aligner">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $reservation) : ?>
                <tr>
                    <td><a href="/reservations/<?= $reservation->id ?>"><?= htmlspecialchars($reservation->motif) ?></a></td>
                    <td><?= htmlspecialchars($reservation->salle?->nom ?? '—') ?></td>
                    <td><?= htmlspecialchars($reservation->responsable) ?></td>
                    <td><?= $reservation->date_debut->format('d/m/Y H:i') ?></td>
                    <td><?= $reservation->date_fin->format('d/m/Y H:i') ?></td>
                    <td>
                        <?php if ($reservation->annule) : ?>
                            <span class="pastille inactif">✖ annulée</span>
                        <?php else : ?>
                            <span class="pastille actif">✔ confirmée</span>
                        <?php endif; ?>
                    </td>
                    <td class="aligner">
                        <a class="bouton secondaire" href="/reservations/<?= $reservation->id ?>">Détail</a>
                        <?php if (!$reservation->annule) : ?>
                            <form class="en-ligne" method="post" action="/reservations/<?= $reservation->id ?>/cancel" onsubmit="return confirm('Annuler cette réservation ?');">
                                <button class="bouton danger" type="submit">Annuler</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>