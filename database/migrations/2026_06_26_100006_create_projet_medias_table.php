<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Galerie photos/vidéos (R6) — hasMany ordonné. Enum applicatif en string (compat SQLite).
    public function up(): void
    {
        Schema::create('projet_medias', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('projet_id')->constrained('projets')->cascadeOnDelete();
            $table->string('type', 10);             // image | video
            $table->string('chemin')->nullable();   // fichier téléversé (image ou vidéo native)
            $table->string('url_embed')->nullable(); // YouTube/Vimeo (si type=video en embed)
            $table->string('legende')->nullable();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('projet_medias');
    }
};
