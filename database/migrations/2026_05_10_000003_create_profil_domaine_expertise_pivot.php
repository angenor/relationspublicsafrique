<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profil_domaine_expertise', function (Blueprint $table): void {
            $table->foreignId('profil_id')->constrained('profils')->cascadeOnDelete();
            $table->foreignId('domaine_expertise_id')->constrained('domaines_expertise')->cascadeOnDelete();
            $table->primary(['profil_id', 'domaine_expertise_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profil_domaine_expertise');
    }
};
