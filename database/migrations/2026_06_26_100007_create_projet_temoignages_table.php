<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Témoignages optionnels (R9) — hasMany ordonné, section masquée si vide.
    public function up(): void
    {
        Schema::create('projet_temoignages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('projet_id')->constrained('projets')->cascadeOnDelete();
            $table->string('auteur');
            $table->string('fonction')->nullable();
            $table->string('organisation')->nullable();
            $table->text('contenu');
            $table->string('photo')->nullable(); // projets/temoignages/…
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projet_temoignages');
    }
};
