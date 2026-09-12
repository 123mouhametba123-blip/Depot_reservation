<?php

/**
 * Liste des salles.
 *
 * @var list<\App\Model\Salle> $salles
 */
$types = [
    'cours'        => ['🎓', 'Cours'],
    'informatique' => ['💻', 'Informatique'],
    'laboratoire'  => ['🧪', 'Laboratoire'],
    'amphitheatre' => ['🎭', 'Amphithéâtre'],
    'reunion'      => ['🤝', 'Réunion'],
];
?>
<div class="actions">
    <h1>Liste des salles</h1>
    <a class="bouton" href="/salles/create">+ Ajouter une salle</a>
</div>

<?php if ($salles === []) : ?>
    <p class="vide">Aucune salle enregistrée pour le moment.</p>
<?php else : ?>
    <div class="tableau-bord">
        <?php foreach ($salles as $salle) : ?>
            <a class="carte salle<?= $salle->active ? '' : ' pas-active' ?>" href="/salles/<?= $salle->id ?>">
                <?php [$icone, $libelle] = $types[$salle->type] ?? ['🏫', 'Autre']; ?>
                <strong><?= $icone ?> <?= htmlspecialchars($salle->nom) ?></strong>
                <span class="salle-meta">
                    <span><?= htmlspecialchars($salle->batiment) ?></span>
                    <span><?= $salle->capacite ?> places</span>
                </span>
                <span class="pastille <?= $salle->active ? 'actif' : 'inactif' ?>"><?= $salle->active ? '● active' : '● inactive' ?></span>
            </a>
        <?php endforeach; ?>
    </div>
<?php endif; ?>