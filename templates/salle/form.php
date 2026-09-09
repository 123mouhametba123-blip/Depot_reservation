<?php
/** @var \App\Model\Salle|null $salle */
/** @var string $action */
/** @var array<string, list<string>> $erreurs */
/** @var array<string, mixed> $anciennes */

$valeur = static function (string $champ, mixed $defaut) use ($salle, $anciennes): mixed {
    if (array_key_exists($champ, $anciennes)) {
        return $anciennes[$champ];
    }
    if ($salle !== null) {
        return $salle->$champ;
    }
    return $defaut;
};

$listeTypes = \App\Model\Salle::TYPES_AUTORISES;
$typeActif = (string) $valeur('type', '');
?>
<h1><?= $salle === null ? 'Ajouter une salle' : 'Modifier la salle' ?></h1>

<form method="post" action="<?= $e($action) ?>" class="formulaire">
    <div class="champ">
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" value="<?= $e($valeur('nom', '')) ?>" required>
        <?php foreach ($erreurs['nom'] ?? [] as $erreur): ?>
            <span class="erreur-champ"><?= $e($erreur) ?></span>
        <?php endforeach; ?>
    </div>

    <div class="champ">
        <label for="batiment">Bâtiment</label>
        <input type="text" id="batiment" name="batiment" value="<?= $e($valeur('batiment', '')) ?>" required>
        <?php foreach ($erreurs['batiment'] ?? [] as $erreur): ?>
            <span class="erreur-champ"><?= $e($erreur) ?></span>
        <?php endforeach; ?>
    </div>

    <div class="champ">
        <label for="capacite">Capacité (places)</label>
        <input type="number" id="capacite" name="capacite" min="1" max="1000"
               value="<?= $e($valeur('capacite', '')) ?>" required>
        <?php foreach ($erreurs['capacite'] ?? [] as $erreur): ?>
            <span class="erreur-champ"><?= $e($erreur) ?></span>
        <?php endforeach; ?>
    </div>

    <div class="champ">
        <label for="type">Type</label>
        <select id="type" name="type" required>
            <option value="">— choisir —</option>
            <?php foreach ($listeTypes as $type): ?>
                <option value="<?= $e($type) ?>" <?= $typeActif === $type ? 'selected' : '' ?>>
                    <?= $e($type) ?>
                </option>
            <?php endforeach; ?>
        </select>
        <?php foreach ($erreurs['type'] ?? [] as $erreur): ?>
            <span class="erreur-champ"><?= $e($erreur) ?></span>
        <?php endforeach; ?>
    </div>

    <div class="champ aligner">
        <label class="case" for="active">
            <input type="checkbox" id="active" name="active"
                   <?= (bool) $valeur('active', false) ? 'checked' : '' ?>>
            Salle active (disponible à la réservation)
        </label>
        <?php foreach ($erreurs['active'] ?? [] as $erreur): ?>
            <span class="erreur-champ"><?= $e($erreur) ?></span>
        <?php endforeach; ?>
    </div>

    <div class="actions">
        <button type="submit" class="bouton">Enregistrer</button>
        <a class="bouton secondaire" href="/salles">Annuler</a>
    </div>
</form>
