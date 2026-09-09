<?php /** @var list<\App\Model\Salle> $salles */ ?>
<h1>Liste des salles</h1>
<p><a class="bouton" href="/salles/create">+ Ajouter une salle</a></p>

<?php if ($salles === []): ?>
    <p class="vide">Aucune salle enregistrée pour le moment.</p>
<?php else: ?>
    <table class="table">
        <thead>
        <tr>
            <th>Nom</th>
            <th>Bâtiment</th>
            <th>Capacité</th>
            <th>Type</th>
            <th>État</th>
            <th class="aligner">Actions</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($salles as $salle): ?>
            <tr>
                <td><a href="/salles/<?= $salle->id ?>"><?= $e($salle->nom) ?></a></td>
                <td><?= $e($salle->batiment) ?></td>
                <td><?= $e($salle->capacite) ?> places</td>
                <td><?= $e($salle->type) ?></td>
                <td>
                    <?php if ($salle->active): ?>
                        <span class="pastille actif">active</span>
                    <?php else: ?>
                        <span class="pastille inactif">inactive</span>
                    <?php endif; ?>
                </td>
                <td class="aligner">
                    <a href="/salles/<?= $salle->id ?>">Détail</a>
                    <a href="/salles/<?= $salle->id ?>/edit">Modifier</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
