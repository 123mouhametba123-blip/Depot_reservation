<?php

declare(strict_types=1);

namespace App\Validator;

use Respect\Validation\Validator as v;


final class ReservationValidator implements ValidatorInterface
{
    public function validate(array $data): ValidationResult
    {
        $erreurs = [];

        $salleId = $data['salle_id'] ?? null;
        if (!v::intVal()->positive()->validate($salleId)) {
            $erreurs['salle_id'][] = 'Le champ "salle" est invalide.';
        }

        $responsable = trim((string) ($data['responsable'] ?? ''));
        if ($responsable === '' || mb_strlen($responsable) < 2 || mb_strlen($responsable) > 120) {
            $erreurs['responsable'][] = 'Le responsable doit contenir entre 2 et 120 caractères.';
        }

        $email = trim((string) ($data['email'] ?? ''));
        if (!v::email()->validate($email)) {
            $erreurs['email'][] = 'L\'adresse électronique est invalide.';
        }

        $motif = trim((string) ($data['motif'] ?? ''));
        if ($motif === '' || mb_strlen($motif) < 5 || mb_strlen($motif) > 255) {
            $erreurs['motif'][] = 'Le motif doit contenir entre 5 et 255 caractères.';
        }

        $dateDebut = $this->dateValide($data['date_debut'] ?? null);
        if ($dateDebut === null) {
            $erreurs['date_debut'][] = 'La date de début est invalide.';
        }

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
