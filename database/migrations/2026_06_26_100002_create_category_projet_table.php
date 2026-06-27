<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Pivot thématiques (R2). Patron : category_media — categories.id est `int`
    // legacy → colonne integer sans FK DB ; FK projet contrainte (cascade).
    public function up(): void
    {
        Schema::create('category_projet', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('projet_id')->constrained('projets')->cascadeOnDelete();
            $table->integer('category_id')->index(); // → categories (type='projet'), sans FK DB
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->unique(['projet_id', 'category_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_projet');
    }
};
