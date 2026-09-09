<?php

declare(strict_types=1);

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Étape 3 — Modèle Eloquent de la table « salles ».
 *
 * SQL: Salle(id, nom, batiment, capacite, type, active, created_at, updated_at)
 */
final class Salle extends Model
{
    public const TYPES_AUTORISES = ['cours', 'informatique', 'laboratoire', 'amphitheatre', 'reunion'];

    protected $table = 'salles';

    /** Propriétés autorisées en affectation de masse. */
    protected $fillable = [
        'nom',
        'batiment',
        'capacite',
        'type',
        'active',
    ];

    /** Conversions de type (capacite:int, active:bool, dates:DateTimeImmutable). */
    protected $casts = [
        'capacite'   => 'integer',
        'active'     => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation : une salle possède plusieurs réservations.
     *
     * Usage : $salle->reservations;
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class, 'salle_id');
    }
}
