<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

/**
 * Migration — Table « reservations ».
 *
 * Reservation(id, salle_id, responsable, email, motif,
 *              date_debut, date_fin, statut, created_at, updated_at)
 *
 * @return callable(): void
 */
return static function (): void {
    if (Capsule::schema()->hasTable('reservations')) {
        Capsule::schema()->drop('reservations');
    }

    Capsule::schema()->create('reservations', static function (Blueprint $table): void {
        $table->increments('id');
        $table->unsignedInteger('salle_id');
        $table->string('responsable', 120);
        $table->string('email', 190);
        $table->string('motif', 255);
        $table->dateTime('date_debut');
        $table->dateTime('date_fin');
        $table->enum('statut', ['confirmee', 'annulee'])->default('confirmee');
        $table->timestamps();

        $table->foreign('salle_id')->references('id')->on('salles')->onDelete('cascade');
        $table->index(['salle_id', 'date_debut', 'date_fin']);
    });
};
