<?php
/** @var list<\App\Model\Salle> $salles */
/** @var array<string, list<string>> $erreurs */
/** @var array<string, mixed> $anciennes */

$valeur = static fn (string $champ): mixed => $anciennes[$champ] ?? '';
?>
<h1>Créer une réservation</h1>

<?php foreach ($erreurs['general'] ?? [] as $erreurGenerale): ?>
    <div class="bien-suivi erreur"><?= $e($erreurGenerale) ?></div>
<?php endforeach; ?>

<form method="post" action="/reservations" class="formulaire">
    <div class="champ">
        <label for="salle_id">Salle</label>
        <select id="salle_id" name="salle_id" required>
            <option value="">— choisir une salle —</option>
            <?php foreach ($salles as $salle): ?>
                <?php if (!$salle->active): continue; endif; ?>
                <option value="<?= $salle->id ?>" <?= (string) $valeur('salle_id') === (string) $salle->id ? 'selected' : '' ?>>
                    <?= $e($salle->nom) ?> (<?= $e($salle->capacite) ?> places)
                </option>
            <?php endforeach; ?>
        </select>
        <?php foreach ($erreurs['salle_id'] ?? [] as $erreur): ?>
            <span class="erreur-champ"><?= $e($erreur) ?></span>
        <?php endforeach; ?>
    </div>

    <div class="champ">
        <label for="responsable">Responsable</label>
        <input type="text" id="responsable" name="responsable" value="<?= $e($valeur('responsable')) ?>" required>
        <?php foreach ($erreurs['responsable'] ?? [] as $erreur): ?>
            <span class="erreur-champ"><?= $e($erreur) ?></span>
        <?php endforeach; ?>
    </div>

    <div class="champ">
        <label for="email">E-mail</label>
        <input type="email" id="email" name="email" value="<?= $e($valeur('email')) ?>" required>
        <?php foreach ($erreurs['email'] ?? [] as $erreur): ?>
            <span class="erreur-champ"><?= $e($erreur) ?></span>
        <?php endforeach; ?>
    </div>

    <div class="champ">
        <label for="motif">Motif</label>
        <input type="text" id="motif" name="motif" value="<?= $e($valeur('motif')) ?>"
               minlength="5" maxlength="255" required>
        <?php foreach ($erreurs['motif'] ?? [] as $erreur): ?>
            <span class="erreur-champ"><?= $e($erreur) ?></span>
        <?php endforeach; ?>
    </div>

    <div class="grille2">
        <div class="champ">
            <label for="date_debut">Début</label>
            <input type="datetime-local" id="date_debut" name="date_debut" value="<?= $e($valeur('date_debut')) ?>" required>
            <?php foreach ($erreurs['date_debut'] ?? [] as $erreur): ?>
                <span class="erreur-champ"><?= $e($erreur) ?></span>
            <?php endforeach; ?>
        </div>

        <div class="champ">
            <label for="date_fin">Fin</label>
            <input type="datetime-local" id="date_fin" name="date_fin" value="<?= $e($valeur('date_fin')) ?>" required>
            <?php foreach ($erreurs['date_fin'] ?? [] as $erreur): ?>
                <span class="erreur-champ"><?= $e($erreur) ?></span>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="actions">
        <button type="submit" class="bouton">Réserver</button>
        <a class="bouton secondaire" href="/reservations">Annuler</a>
    </div>
</form>
