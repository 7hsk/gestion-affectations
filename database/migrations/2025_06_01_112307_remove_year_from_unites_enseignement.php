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
        Schema::table('unites_enseignement', function (Blueprint $table) {
            // Remove year-specific column - UEs are now general
            $table->dropColumn('annee_universitaire');

            // Add a note that years are now handled through affectations table
            // UEs are general and can be used for any year through affectations
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unites_enseignement', function (Blueprint $table) {
            // Restore the year column if needed
            $table->string('annee_universitaire', 10)->nullable();
        });
    }
};
