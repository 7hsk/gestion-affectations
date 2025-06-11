<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update the validee enum to include 'annule'
        DB::statement("ALTER TABLE affectations MODIFY COLUMN validee ENUM('en_attente', 'valide', 'rejete', 'refuse', 'annule') NOT NULL DEFAULT 'en_attente'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert back to original enum values
        DB::statement("ALTER TABLE affectations MODIFY COLUMN validee ENUM('en_attente', 'valide', 'rejete', 'refuse') NOT NULL DEFAULT 'en_attente'");
    }
};
