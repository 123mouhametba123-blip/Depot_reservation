<?php

/**
 * Formulaire d'une salle.
 *
 * @var \App\Model\Salle|null $salle
 * @var string $action
 * @var array<string, mixed> $erreurs
 * @var array<string, mixed> $anciennes
 */
$listeTypes = [
    'cours'        => ['🎓', 'Cours'],
    'informatique' => ['💻', 'Informatique'],
    'laboratoire'  => ['🧪', 'Laboratoire'],
    'amphitheatre' => ['🎭', 'Amphithéâtre'],
    'reunion'      => ['🤝', 'Réunion'],
];

$valeur = static function (string $clef) use ($salle, $anciennes) {
    if (array_key_exists($clef, $anciennes)) {
        return is_array($anciennes[$clef])
            ? implode(', ', $anciennes[$clef])
            : (string) $anciennes[$clef];
    }
    return (string) ($salle->{$clef} ?? '');
};
?>
<div class="actions">
    <h1><?= $salle === null ? 'Ajouter une salle' : 'Modifier la salle' ?></h1>
</div>

<form class="formulaire" method="post" action="<?= $action ?>">
    <?php if (isset($erreurs['general'])) : ?>
        <div class="bien-suivi erreur"><?= htmlspecialchars((string) $erreurs['general'][0]) ?></div>
    <?php endif; ?>

    <div class="grille2">
        <div class="champ">
            <label for="nom">Nom</label>
            <input type="text" name="nom" id="nom" value="<?= htmlspecialchars($valeur('nom')) ?>">
            <?php if (isset($erreurs['nom'])) : ?>
                <span class="erreur-champ"><?= htmlspecialchars((string) $erreurs['nom'][0]) ?></span>
            <?php endif; ?>
        </div>
        <div class="champ">
            <label for="batiment">Bâtiment</label>
            <input type="text" name="batiment" id="batiment" value="<?= htmlspecialchars($valeur('batiment')) ?>">
            <?php if (isset($erreurs['batiment'])) : ?>
                <span class="erreur-champ"><?= htmlspecialchars((string) $erreurs['batiment'][0]) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="grille2">
        <div class="champ">
            <label for="capacite">Capacité</label>
            <input type="number" name="capacite" id="capacite" min="1" value="<?= htmlspecialchars($valeur('capacite')) ?>">
            <?php if (isset($erreurs['capacite'])) : ?>
                <span class="erreur-champ"><?= htmlspecialchars((string) $erreurs['capacite'][0]) ?></span>
            <?php endif; ?>
        </div>
        <div class="champ">
            <label for="type">Type</label>
            <select name="type" id="type">
                <?php foreach ($listeTypes as $clef => [$icone, $libelle]) : ?>
                    <option value="<?= $clef ?>" <?= $valeur('type') === $clef ? 'selected' : '' ?>><?= $icone ?> <?= $libelle ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="champ case">
        <input type="checkbox" name="active" id="active" <?= $valeur('active') === '1' ? 'checked' : '' ?>>
        <label for="active">Salle active (réservable)</label>
    </div>

    <div class="actions">
        <button class="bouton succes" type="submit">💾 Enregistrer</button>
        <a class="bouton secondaire" href="/salles">Annuler</a>
    </div>
</form>