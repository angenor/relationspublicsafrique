<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Médias post-événement (research R6) — replay vidéo, photos, document.
 * hasMany ordonné. Miroir de la table `projet_medias`. Enum applicatif en
 * string (compat SQLite). N'a de sens que pour un événement clos.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_medias', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('type', 20);              // replay | image | document
            $table->string('chemin')->nullable();    // fichier téléversé (image, vidéo native, document)
            $table->string('url_embed')->nullable(); // YouTube/Vimeo (replay en embed)
            $table->string('legende')->nullable();
            $table->unsignedInteger('position')->default(0)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_medias');
    }
};
