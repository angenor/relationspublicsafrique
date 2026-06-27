<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Pivot many-to-many projets ↔ partenaires (R7).
    public function up(): void
    {
        Schema::create('partenaire_projet', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('projet_id')->constrained('projets')->cascadeOnDelete();
            $table->foreignId('partenaire_id')->constrained('partenaires')->cascadeOnDelete();
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->unique(['projet_id', 'partenaire_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partenaire_projet');
    }
};
