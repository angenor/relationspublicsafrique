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
        Schema::create('formation_reviews', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->unsignedBigInteger('formation_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('formation_id')->references('id')->on('formations')->onDelete('cascade');
            $table->integer('rating')->unsigned(); // Note de 1 à 5
            $table->text('comment')->nullable();
            $table->boolean('is_verified')->default(false); // Vérifier si l'utilisateur a vraiment suivi la formation
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['formation_id', 'rating']);
            $table->unique(['user_id', 'formation_id']); // Un utilisateur ne peut évaluer qu'une fois
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formation_reviews');
    }
};
