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
        Schema::table('affectations', function (Blueprint $table) {
            // Change type_seance to allow multiple selections
            $table->string('type_seance', 20)->change(); // Allow comma-separated values like "CM,TD" or "CM,TD,TP"

            // Add validation column for chef approval
            $table->integer('validee_par')->nullable()->after('validee');
            $table->timestamp('date_validation')->nullable()->after('validee_par');
            $table->text('commentaire')->nullable()->after('date_validation');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('affectations', function (Blueprint $table) {
            // Revert type_seance to enum
            $table->enum('type_seance', ['CM', 'TD', 'TP'])->change();

            // Remove added columns
            $table->dropColumn(['validee_par', 'date_validation', 'commentaire']);
        });
    }
};
