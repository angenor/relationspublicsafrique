<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Table master réutilisable des partenaires (R7).
    public function up(): void
    {
        Schema::create('partenaires', function (Blueprint $table): void {
            $table->id();
            $table->string('nom');
            $table->string('slug')->nullable()->unique();
            $table->string('logo')->nullable(); // projets/partenaires/…
            $table->string('url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('partenaires');
    }
};
