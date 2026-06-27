<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Chiffres clés / impact (R8) — hasMany ordonné.
    public function up(): void
    {
        Schema::create('projet_resultats', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('projet_id')->constrained('projets')->cascadeOnDelete();
            $table->string('libelle');           // ex. « Bénéficiaires formés »
            $table->string('valeur');            // string : formats libres (%, +, etc.)
            $table->string('unite')->nullable(); // ex. « personnes »
            $table->string('icone')->nullable(); // nom d'icône optionnel
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projet_resultats');
    }
};
