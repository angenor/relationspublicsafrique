<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profil_tag', function (Blueprint $table): void {
            $table->foreignId('profil_id')->constrained('profils')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('tags')->cascadeOnDelete();
            $table->primary(['profil_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profil_tag');
    }
};
