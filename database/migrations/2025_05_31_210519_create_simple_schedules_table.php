<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop existing schedules table if it exists
        Schema::dropIfExists('schedules');

        // Create new schedules table without foreign key constraints
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->integer('ue_id');
            $table->integer('user_id')->nullable();
            $table->integer('filiere_id');
            $table->enum('jour_semaine', ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi']);
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->enum('type_seance', ['CM', 'TD', 'TP']);
            $table->string('salle')->nullable();
            $table->string('semestre', 10);
            $table->string('annee_universitaire', 20)->default('2024-2025');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
