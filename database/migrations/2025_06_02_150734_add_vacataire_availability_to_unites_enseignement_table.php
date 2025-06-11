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
            // Add field to track which types are available for vacataires
            // JSON field to store array like ["CM", "TD", "TP"] or null if not available
            $table->json('vacataire_types')->nullable()->after('est_vacant');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('unites_enseignement', function (Blueprint $table) {
            $table->dropColumn('vacataire_types');
        });
    }
};
