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
        Schema::table('historique_affectations', function (Blueprint $table) {
            $table->string('action')->nullable()->after('annee_universitaire');
            $table->text('description')->nullable()->after('action');
            $table->json('changes')->nullable()->after('description');
            $table->string('created_by_name')->nullable()->after('changes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('historique_affectations', function (Blueprint $table) {
            $table->dropColumn(['action', 'description', 'changes', 'created_by_name']);
        });
    }
};
