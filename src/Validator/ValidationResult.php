<?php

declare(strict_types=1);

namespace App\Validator;


final class ValidationResult
{
    /**
     * @param array<string, list<string>> $erreurs message(s) par champ
     * @param array<string, mixed>        $donnees données acceptées
     */
    public function __construct(
        private readonly array $erreurs,
        private readonly array $donnees = [],
    ) {
    }

    public static function valide(array $donnees): self
    {
        return new self([], $donnees);
    }

    /** @param array<string, list<string>> $erreurs */
    public static function invalide(array $erreurs): self
    {
        return new self($erreurs);
    }

    public function isValid(): bool
    {
        return $this->erreurs === [];
    }

    /** @return array<string, list<string>> */
    public function errors(): array
    {
        return $this->erreurs;
    }

    /** @return array<string, mixed> */
    public function getDonnees(): array
    {
        return $this->donnees;
    }
}
