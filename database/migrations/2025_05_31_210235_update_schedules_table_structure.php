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
        Schema::table('schedules', function (Blueprint $table) {
            // Add missing columns
            $table->foreignId('filiere_id')->nullable()->after('ue_id')->constrained('filieres')->onDelete('cascade');
            $table->enum('type_seance', ['CM', 'TD', 'TP'])->after('heure_fin');
            $table->string('annee_universitaire', 20)->after('semestre');
            $table->timestamp('updated_at')->nullable()->after('created_at');

            // Modify user_id to be nullable
            $table->unsignedBigInteger('user_id')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropForeign(['filiere_id']);
            $table->dropColumn(['filiere_id', 'type_seance', 'annee_universitaire', 'updated_at']);
        });
    }
};
