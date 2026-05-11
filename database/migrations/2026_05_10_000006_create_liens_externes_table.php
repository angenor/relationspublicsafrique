<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('liens_externes', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('profil_id')->constrained('profils')->cascadeOnDelete();
            $table->enum('type', ['linkedin', 'site_web', 'portfolio', 'facebook', 'twitter', 'youtube', 'autre']);
            $table->string('url', 500);
            $table->string('libelle', 150)->nullable();
            $table->timestamps();

            $table->index('profil_id', 'idx_liens_profil');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('liens_externes');
    }
};
