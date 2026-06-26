<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('media_comments', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('media_id')->constrained('media')->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('media_comments')->nullOnDelete();
            $table->string('author_name', 120);
            $table->string('author_email');
            $table->text('body');
            $table->string('status', 16)->default('pending'); // pending | approved | rejected
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();

            $table->index(['media_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media_comments');
    }
};
