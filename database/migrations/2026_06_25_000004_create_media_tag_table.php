<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Patron exact : profil_tag (2026_05_10_000005_create_profil_tag_pivot). Réutilise la table tags.
    public function up(): void
    {
        Schema::create('media_tag', function (Blueprint $table): void {
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();
            $table->primary(['media_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_tag');
    }
};
