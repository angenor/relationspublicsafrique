<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demandes_moderation', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('profil_id')->constrained('profils')->cascadeOnDelete();
            $table->integer('soumis_par');
            $table->foreign('soumis_par')->references('id')->on('users')->cascadeOnDelete();
            $table->integer('moderateur_id')->nullable();
            $table->foreign('moderateur_id')->references('id')->on('users')->nullOnDelete();
            // string plutôt qu'enum : valeurs utilisées par ProfilModerationService
            // (soumis, approuve, rejete, archive) — validation côté code.
            $table->string('decision', 20)->default('soumis');
            $table->text('motif')->nullable();
            $table->timestamps();

            $table->index(['profil_id', 'decision'], 'idx_modera_profil_decision');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demandes_moderation');
    }
};
