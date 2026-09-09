<?php /** @var list<\App\Model\Reservation> $reservations @var list<\App\Model\Salle> $salles @var int|null $salleFiltre */ ?>
<h1>Réservations</h1>

<form method="get" action="/reservations" class="filtre">
    <label for="salle">Filtrer par salle</label>
    <select id="salle" name="salle">
        <option value="">Toutes les salles</option>
        <?php foreach ($salles as $salle): ?>
            <option value="<?= $salle->id ?>" <?= $salleFiltre === (int) $salle->id ? 'selected' : '' ?>>
                <?= $e($salle->nom) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <button type="submit" class="bouton">Filtrer</button>
    <a class="bouton secondaire" href="/reservations">Réinitialiser</a>
    <a class="bouton" href="/reservations/create">+ Nouvelle réservation</a>
</form>

<?php if ($reservations === []): ?>
    <p class="vide">Aucune réservation trouvée.</p>
<?php else: ?>
    <table class="table">
        <thead>
        <tr>
            <th>Salle</th>
            <th>Responsable</th>
            <th>Motif</th>
            <th>Début</th>
            <th>Fin</th>
            <th>Statut</th>
            <th class="aligner">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($reservations as $reservation): ?>
            <tr>
                <td><?= $e($reservation->salle?->nom ?? '-') ?></td>
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
                <td class="aligner">
                    <a href="/reservations/<?= $reservation->id ?>">Détail</a>
                    <?php if ($reservation->statut === \App\Model\Reservation::STATUT_CONFIRMEE): ?>
                        <form method="post" action="/reservations/<?= $reservation->id ?>/cancel" class="en-ligne"
                              onsubmit="return confirm('Annuler cette réservation ?');">
                            <button type="submit" class="bouton danger">Annuler</button>
                        </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
