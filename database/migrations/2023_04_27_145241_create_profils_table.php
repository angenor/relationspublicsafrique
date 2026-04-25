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
        Schema::create('profils', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('slug')->unique();
            $table->string('nom');
            $table->string('prenom')->nullable();
            $table->string('title')->nullable();
            $table->string('fonction')->nullable();
            $table->string('domaine')->nullable();
            $table->string('facebook')->nullable();
            $table->string('twitter')->nullable();
            $table->string('youtube')->nullable();
            $table->string('linkding')->nullable();
            $table->string('site')->nullable();
            $table->string('contact')->nullable();
            $table->string('adresse')->nullable();
            $table->string('tel')->nullable();
            $table->string('email')->nullable();
            $table->string('cover')->nullable();

            $table->longText('bio')->nullable();;

            $table->string('image')->nullable()->default('images/user.png');

            $table->integer('online')->default(0);
            $table->integer('aprouve')->default(0);

            $table->foreignIdFor(\App\Models\Pays::class) ;
            $table->foreignIdFor(\App\Models\User::class) ;

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profils');
    }
};
