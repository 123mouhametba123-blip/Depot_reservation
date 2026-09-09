<?php

declare(strict_types=1);

namespace App\Validator;

/**
 * Étape 5 — Contrat commun de validation.
 *
 * Tous les validateurs de formulaires implémentent cette interface et
 * retournent un objet ValidationResult. Aucun validateur ne persiste les
 * données : il se contente de les contrôler.
 */
interface ValidatorInterface
{
    public function validate(array $data): ValidationResult;
}
