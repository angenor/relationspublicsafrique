<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Intervenants d'un événement (research R5) — hasMany ordonné par `position`.
 * `event_id` est une vraie FK : `events.id` est un bigIncrements.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_speakers', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('event_id')->constrained('events')->cascadeOnDelete();
            $table->string('nom');
            $table->string('role')->nullable();
            $table->string('organisation')->nullable();
            $table->string('photo')->nullable();
            $table->text('bio')->nullable();
            $table->unsignedInteger('position')->default(0)->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_speakers');
    }
};
