<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historique_profils', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('profil_id')->constrained('profils')->cascadeOnDelete();
            $table->integer('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
            // string(40) plutôt qu'enum : permet d'évoluer la liste d'actions
            // (consentement_atteste, soumis_moderation, archive…) sans
            // migration destructive. La validation des valeurs est faite
            // côté code (ProfilHistoriqueService).
            $table->string('action', 40);
            $table->json('diff')->nullable();
            $table->text('motif')->nullable();
            $table->string('ip', 45)->nullable();
            $table->string('user_agent', 255)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['profil_id', 'created_at'], 'idx_hist_profil_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historique_profils');
    }
};
