<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('widget_texts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('key');
            $table->string('slug');
            $table->string('link')->nullable();
            $table->longText('content')->nullable();
            $table->text('resume')->nullable();
            $table->integer('position')->default(0)->nullable();
            $table->integer('online')->default(1)->nullable();
            $table->string('image')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('widget_texts');
    }
};
