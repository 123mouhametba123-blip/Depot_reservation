<?php

/**
 * Détail d'une salle.
 *
 * @var \App\Model\Salle $salle
 * @var Illuminate\Support\Collection $reservations
 */
$types = [
    'cours'        => ['🎓', 'Cours'],
    'informatique' => ['💻', 'Informatique'],
    'laboratoire'  => ['🧪', 'Laboratoire'],
    'amphitheatre' => ['🎭', 'Amphithéâtre'],
    'reunion'      => ['🤝', 'Réunion'],
];

[$iconeType, $libelleType] = $types[$salle->type] ?? ['🏫', 'Autre'];
?>
<div class="actions">
    <h1><?= $iconeType ?> <?= htmlspecialchars($salle->nom) ?></h1>
    <div class="card-actions">
        <span class="pastille <?= $salle->active ? 'actif' : 'inactif' ?>"><?= $salle->active ? '● active' : '● inactive' ?></span>
        <?php if ($salle->active) : ?>
            <a class="bouton" href="/reservations/create?salle=<?= $salle->id ?>">Réserver cette salle</a>
        <?php endif; ?>
        <a class="bouton secondaire" href="/salles/<?= $salle->id ?>/edit">Modifier</a>
    </div>
</div>

<div class="detail">
    <table>
        <tr><th>Bâtiment</th><td><?= htmlspecialchars($salle->batiment) ?></td></tr>
        <tr><th>Capacité</th><td><?= $salle->capacite ?> places</td></tr>
        <tr><th>Type</th><td><span class="badge-type t-<?= $salle->type ?>"><?= $iconeType ?> <?= $libelleType ?></span></td></tr>
        <tr><th>Statut</th><td><span class="pastille <?= $salle->active ? 'actif' : 'inactif' ?>"><?= $salle->active ? 'Actif' : 'Inactif' ?></span></td></tr>
    </table>
</div>

<h2>Réservations de cette salle</h2>
<?php if ($reservations->isEmpty()) : ?>
    <p class="vide">Aucune réservation pour cette salle pour le moment.</p>
<?php else : ?>
    <table class="table">
        <thead>
            <tr>
                <th>Motif</th>
                <th>Responsable</th>
                <th>Début</th>
                <th>Fin</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $r) : ?>
                <tr>
                    <td><a href="/reservations/<?= $r->id ?>"><?= htmlspecialchars($r->motif) ?></a></td>
                    <td><?= htmlspecialchars($r->responsable) ?></td>
                    <td><?= $r->date_debut->format('d/m/Y H:i') ?></td>
                    <td><?= $r->date_fin->format('d/m/Y H:i') ?></td>
                    <td>
                        <?php if ($r->annule) : ?>
                            <span class="pastille inactif">✖ annulée</span>
                        <?php else : ?>
                            <span class="pastille actif">✔ confirmée</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>