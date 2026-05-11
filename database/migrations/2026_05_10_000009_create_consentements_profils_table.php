<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consentements_profils', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('profil_id')->unique()->constrained('profils')->cascadeOnDelete();
            $table->integer('atteste_par');
            $table->foreign('atteste_par')->references('id')->on('users')->cascadeOnDelete();
            $table->timestamp('atteste_le');
            $table->string('email_notification_envoye_a', 200)->nullable();
            $table->timestamp('email_notification_envoye_le')->nullable();
            $table->string('jeton_retrait', 64)->nullable()->unique();
            $table->timestamp('jeton_expire_le')->nullable();
            $table->timestamp('retrait_demande_le')->nullable();
            $table->timestamps();

            $table->index('jeton_retrait', 'idx_consent_jeton');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consentements_profils');
    }
};
