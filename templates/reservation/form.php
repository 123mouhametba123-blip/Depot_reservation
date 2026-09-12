<?php

/**
 * Formulaire de réservation.
 *
 * @var \App\Model\Reservation|null $reservation
 * @var list<\App\Model\Salle> $salles
 * @var string $action
 * @var array<string, mixed> $erreurs
 * @var array<string, mixed> $anciennes
 */
$valeur = static function (string $clef) use ($reservation, $anciennes) {
    if (array_key_exists($clef, $anciennes)) {
        return is_array($anciennes[$clef])
            ? implode(', ', $anciennes[$clef])
            : (string) $anciennes[$clef];
    }
    return (string) ($reservation->{$clef} ?? '');
};
?>
<div class="actions">
    <h1><?= $reservation === null ? 'Nouvelle réservation' : 'Modifier la réservation' ?></h1>
</div>

<form class="formulaire" method="post" action="<?= $action ?>">
    <?php if (isset($erreurs['general'])) : ?>
        <div class="bien-suivi erreur"><?= htmlspecialchars((string) $erreurs['general'][0]) ?></div>
    <?php endif; ?>

    <div class="champ">
        <label for="salle_id">Salle</label>
        <?php if ($reservation === null) : ?>
            <select name="salle_id" id="salle_id">
                <?php foreach ($salles as $salle) : ?>
                    <?php if ($salle->active) : ?>
                        <option value="<?= $salle->id ?>" <?= (string) $salle->id === $valeur('salle_id') ? 'selected' : '' ?>>
                            <?= htmlspecialchars($salle->nom) ?> — <?= htmlspecialchars($salle->batiment) ?> (<?= $salle->capacite ?> places)
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        <?php else : ?>
            <input type="text" value="<?= htmlspecialchars((string) ($reservation->salle?->nom ?? '')) ?>" disabled>
        <?php endif; ?>
    </div>

    <div class="grille2">
        <div class="champ">
            <label for="responsable">Responsable</label>
            <input type="text" name="responsable" id="responsable" value="<?= htmlspecialchars($valeur('responsable')) ?>">
            <?php if (isset($erreurs['responsable'])) : ?>
                <span class="erreur-champ"><?= htmlspecialchars((string) $erreurs['responsable'][0]) ?></span>
            <?php endif; ?>
        </div>
        <div class="champ">
            <label for="email">Email</label>
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($valeur('email')) ?>">
            <?php if (isset($erreurs['email'])) : ?>
                <span class="erreur-champ"><?= htmlspecialchars((string) $erreurs['email'][0]) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="champ">
        <label for="motif">Motif</label>
        <input type="text" name="motif" id="motif" value="<?= htmlspecialchars($valeur('motif')) ?>">
        <?php if (isset($erreurs['motif'])) : ?>
            <span class="erreur-champ"><?= htmlspecialchars((string) $erreurs['motif'][0]) ?></span>
        <?php endif; ?>
    </div>

    <div class="grille2">
        <div class="champ">
            <label for="date_debut">Début</label>
            <input type="datetime-local" name="date_debut" id="date_debut" value="<?= htmlspecialchars($valeur('date_debut')) ?>">
            <?php if (isset($erreurs['date_debut'])) : ?>
                <span class="erreur-champ"><?= htmlspecialchars((string) $erreurs['date_debut'][0]) ?></span>
            <?php endif; ?>
        </div>
        <div class="champ">
            <label for="date_fin">Fin</label>
            <input type="datetime-local" name="date_fin" id="date_fin" value="<?= htmlspecialchars($valeur('date_fin')) ?>">
            <?php if (isset($erreurs['date_fin'])) : ?>
                <span class="erreur-champ"><?= htmlspecialchars((string) $erreurs['date_fin'][0]) ?></span>
            <?php endif; ?>
        </div>
    </div>

    <div class="actions">
        <button class="bouton succes" type="submit">🗓️ Enregistrer</button>
        <a class="bouton secondaire" href="<?= $reservation === null ? '/reservations' : '/reservations/' . $reservation->id ?>">Annuler</a>
    </div>
</form>