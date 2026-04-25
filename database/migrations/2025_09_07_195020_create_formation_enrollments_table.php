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
        Schema::create('formation_enrollments', function (Blueprint $table) {
            $table->id();
            $table->integer('user_id');
            $table->unsignedBigInteger('formation_id');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('formation_id')->references('id')->on('formations')->onDelete('cascade');
            $table->enum('status', ['enrolled', 'completed', 'dropped'])->default('enrolled');
            $table->integer('progress')->default(0); // Pourcentage de progression
            $table->timestamp('enrolled_at')->useCurrent();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('last_accessed_at')->nullable();
            $table->json('completed_chapters')->nullable(); // Chapitres complétés
            $table->decimal('final_grade', 5, 2)->nullable(); // Note finale
            $table->text('certificate_url')->nullable(); // URL du certificat
            $table->timestamps();

            // Index pour optimiser les requêtes
            $table->index(['user_id', 'formation_id']);
            $table->index(['formation_id', 'status']);
            $table->unique(['user_id', 'formation_id']); // Un utilisateur ne peut s'inscrire qu'une fois
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('formation_enrollments');
    }
};
