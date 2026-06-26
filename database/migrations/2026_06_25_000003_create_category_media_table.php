<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Patron : category_post (2023_08_26_165106_create_category_posts_table), enrichi d'une position.
    public function up(): void
    {
        Schema::create('category_media', function (Blueprint $table): void {
            $table->id();
            // categories.id est `int` (legacy) → colonne integer sans FK (patron category_post).
            $table->integer('category_id')->index();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->unique(['category_id', 'media_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('category_media');
    }
};
