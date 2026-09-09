<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

/**
 * Migration — Table « salles ».
 *
 * Salle(id, nom, batiment, capacite, type, active, created_at, updated_at)
 *
 * @return callable(): void
 */
return static function (): void {
    if (Capsule::schema()->hasTable('salles')) {
        Capsule::schema()->drop('salles');
    }

    Capsule::schema()->create('salles', static function (Blueprint $table): void {
        $table->increments('id');
        $table->string('nom', 100);
        $table->string('batiment', 100);
        $table->unsignedInteger('capacite');
        $table->enum('type', ['cours', 'informatique', 'laboratoire', 'amphitheatre', 'reunion']);
        $table->boolean('active')->default(true);
        $table->timestamps();
    });
};
