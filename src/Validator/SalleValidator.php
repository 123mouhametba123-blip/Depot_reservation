<?php

declare(strict_types=1);

namespace App\Validator;

use App\Model\Salle;
use Respect\Validation\Validator as v;

/**
 * Étape 5 — Validation syntaxique d'une salle (Respect\Validation).
 *
 * Validation d'une salle :
 *  - nom      : obligatoire, 2 à 100 caractères ;
 *  - batiment : obligatoire, 2 à 100 caractères ;
 *  - capacite : entier compris entre 1 et 1 000 ;
 *  - type     : valeur autorisée ;
 *  - active   : booléen.
 */
final class SalleValidator implements ValidatorInterface
{
    public function validate(array $data): ValidationResult
    {
        $erreurs = [];

        // nom
        $nom = trim((string) ($data['nom'] ?? ''));
        if ($nom === '' || mb_strlen($nom) < 2 || mb_strlen($nom) > 100) {
            $erreurs['nom'][] = 'Le nom doit contenir entre 2 et 100 caractères.';
        }

        // batiment
        $batiment = trim((string) ($data['batiment'] ?? ''));
        if ($batiment === '' || mb_strlen($batiment) < 2 || mb_strlen($batiment) > 100) {
            $erreurs['batiment'][] = 'Le bâtiment doit contenir entre 2 et 100 caractères.';
        }

        // capacite
        $capacite = $data['capacite'] ?? null;
        if (!v::intVal()->between(1, 1000)->validate($capacite)) {
            $erreurs['capacite'][] = 'La capacité doit être un entier compris entre 1 et 1000.';
        }

        // type
        $type = trim((string) ($data['type'] ?? ''));
        if (!in_array($type, Salle::TYPES_AUTORISES, true)) {
            $erreurs['type'][] = 'Le type de salle est invalide.';
        }

        // active
        $actif = $data['active'] ?? false;
        if (is_string($actif)) {
            $actif = filter_var($actif, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
        }
        if (!v::boolType()->validate($actif)) {
            $erreurs['active'][] = 'Le champ "active" doit être un booléen.';
        }

        if ($erreurs !== []) {
            return ValidationResult::invalide($erreurs);
        }

        return ValidationResult::valide([
            'nom'      => $nom,
            'batiment' => $batiment,
            'capacite' => (int) $capacite,
            'type'     => $type,
            'active'   => (bool) $actif,
        ]);
    }
}
