<?php

declare(strict_types=1);

/**
 * Script de données initiales (seeder) — insère au moins cinq salles.
 *
 * Idempotent : exécutable plusieurs fois sans créer de doublons.
 * On vérifie l'existence par nom avant chaque insertion.
 *
 * Usage : php database/seed.php
 */

use App\Model\Salle;

require dirname(__DIR__) . '/vendor/autoload.php';

$demarrer = require dirname(__DIR__) . '/config/database.php';
$demarrer();

$salles = [
    ['nom' => 'Amphithéâtre A',    'batiment' => 'Bâtiment A', 'capacite' => 250, 'type' => 'amphitheatre'],
    ['nom' => 'Salle B12',          'batiment' => 'Bâtiment B', 'capacite' => 40,  'type' => 'cours'],
    ['nom' => 'Laboratoire Chimie', 'batiment' => 'Bâtiment C', 'capacite' => 24,  'type' => 'laboratoire'],
    ['nom' => 'Salle Informatique 1','batiment' => 'Bâtiment C', 'capacite' => 30, 'type' => 'informatique'],
    ['nom' => 'Salle de réunion',   'batiment' => 'Bâtiment A', 'capacite' => 12,  'type' => 'reunion'],
];

$ajoutees = 0;

foreach ($salles as $donnees) {
    $existe = Salle::query()->where('nom', $donnees['nom'])->exists();

    if ($existe) {
        echo "Déjà présente  : {$donnees['nom']}\n";
        continue;
    }

    $salle = new Salle();
    $salle->nom      = $donnees['nom'];
    $salle->batiment = $donnees['batiment'];
    $salle->capacite = $donnees['capacite'];
    $salle->type     = $donnees['type'];
    $salle->active   = true;
    $salle->save();

    $ajoutees++;
    echo "Ajoutée        : {$donnees['nom']}\n";
}

echo "Terminé : {$ajoutees} salle(s) ajoutée(s).\n";
