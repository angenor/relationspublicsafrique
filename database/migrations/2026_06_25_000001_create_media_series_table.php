<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_series', function (Blueprint $table): void {
            $table->id();
            $table->string('titre');
            $table->string('slug')->unique();
            $table->string('titre_normalise', 200)->nullable();
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->string('type', 16)->nullable(); // podcast | video | mixte
            $table->boolean('online')->default(true);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->index('titre_normalise', 'idx_media_series_titre_norm');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_series');
    }
};
