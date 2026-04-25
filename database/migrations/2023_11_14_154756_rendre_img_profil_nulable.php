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
        // Dans votre fichier de migration
        Schema::table('profils', function (Blueprint $table) {
            // Supprimer la colonne existante
            $table->dropColumn('image');

            // Ajouter une nouvelle colonne avec les nouvelles spécifications
            $table->string('image')->nullable()->default('images/user.png');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropColumns('profils', ['image']);
    }
};
