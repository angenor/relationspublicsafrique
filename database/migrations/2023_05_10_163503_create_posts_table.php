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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->nullable();;
            $table->integer('position')->default(0)->nullable();
            $table->text('content')->nullable();
            $table->text('resume')->nullable();
            $table->integer('online')->default(0);
            $table->string('type',50)->nullable();
            $table->integer('note')->default(0);
            $table->integer('view')->default(0);
            $table->integer('user_id')->nullable();
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('parent_id')->default(0);
            $table->string('image')->nullable();
            $table->string('externe_link')->nullable();
            $table->dateTime('dateevente')->default(now());
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
