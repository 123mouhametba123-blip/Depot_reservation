<?php

declare(strict_types=1);

namespace App\Validator;

use Respect\Validation\Validator as v;

/**
 * Étape 5 — Validation syntaxique d'une réservation (Respect\Validation).
 *
 * Validation d'une réservation :
 *  - salle_id    : entier positif ;
 *  - responsable : 2 à 120 caractères ;
 *  - email       : adresse valide ;
 *  - motif       : 5 à 255 caractères ;
 *  - date_debut  : date valide ;
 *  - date_fin    : date valide.
 *
 * La comparaison entre les dates (début < fin, durée, futur) est laissée
 * à la couche métier (CreerReservationService), pas ici.
 */
final class ReservationValidator implements ValidatorInterface
{
    public function validate(array $data): ValidationResult
    {
        $erreurs = [];

        // salle_id
        $salleId = $data['salle_id'] ?? null;
        if (!v::intVal()->positive()->validate($salleId)) {
            $erreurs['salle_id'][] = 'Le champ "salle" est invalide.';
        }

        // responsable
        $responsable = trim((string) ($data['responsable'] ?? ''));
        if ($responsable === '' || mb_strlen($responsable) < 2 || mb_strlen($responsable) > 120) {
            $erreurs['responsable'][] = 'Le responsable doit contenir entre 2 et 120 caractères.';
        }

        // email
        $email = trim((string) ($data['email'] ?? ''));
        if (!v::email()->validate($email)) {
            $erreurs['email'][] = 'L\'adresse électronique est invalide.';
        }

        // motif
        $motif = trim((string) ($data['motif'] ?? ''));
        if ($motif === '' || mb_strlen($motif) < 5 || mb_strlen($motif) > 255) {
            $erreurs['motif'][] = 'Le motif doit contenir entre 5 et 255 caractères.';
        }

        // date_debut
        $dateDebut = $this->dateValide($data['date_debut'] ?? null);
        if ($dateDebut === null) {
            $erreurs['date_debut'][] = 'La date de début est invalide.';
        }

        // date_fin
        $dateFin = $this->dateValide($data['date_fin'] ?? null);
        if ($dateFin === null) {
            $erreurs['date_fin'][] = 'La date de fin est invalide.';
        }

        if ($erreurs !== []) {
            return ValidationResult::invalide($erreurs);
        }

        return ValidationResult::valide([
            'salle_id'    => (int) $salleId,
            'responsable' => $responsable,
            'email'       => $email,
            'motif'       => $motif,
            'date_debut'  => $dateDebut,
            'date_fin'    => $dateFin,
        ]);
    }

    /**
     * Retourne un DateTimeImmutable si la chaîne est une date valide.
     */
    private function dateValide(mixed $valeur): ?\DateTimeImmutable
    {
        if (!is_string($valeur) || $valeur === '') {
            return null;
        }

        try {
            return new \DateTimeImmutable($valeur);
        } catch (\Throwable) {
            return null;
        }
    }
}
