<?php

declare(strict_types=1);

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Étape 3 — Modèle Eloquent de la table « reservations ».
 *
 * SQL: Reservation(id, salle_id, responsable, email, motif,
 *                  date_debut, date_fin, statut, created_at, updated_at)
 */
final class Reservation extends Model
{
    public const STATUT_CONFIRMEE = 'confirmee';
    public const STATUT_ANNULEE   = 'annulee';

    protected $table = 'reservations';

    protected $fillable = [
        'salle_id',
        'responsable',
        'email',
        'motif',
        'date_debut',
        'date_fin',
        'statut',
    ];

    protected $casts = [
        'salle_id'   => 'integer',
        'date_debut' => 'datetime',
        'date_fin'   => 'datetime',
        'statut'     => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relation : une réservation appartient à une salle.
     *
     * Usage : $reservation->salle;
     */
    public function salle(): BelongsTo
    {
        return $this->belongsTo(Salle::class, 'salle_id');
    }
}
