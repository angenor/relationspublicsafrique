<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Co-auteurs / intervenants. L'auteur principal reste media.user_id.
    public function up(): void
    {
        Schema::create('media_authors', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            // users.id est `int` (legacy) → colonne integer sans FK.
            $table->integer('user_id')->index();
            $table->string('role', 16)->default('auteur'); // auteur | interviewer | invite
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->unique(['media_id', 'user_id', 'role']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_authors');
    }
};
