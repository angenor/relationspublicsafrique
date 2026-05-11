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
        // SQLite (tests in-memory) ne supporte pas drop+add d'une même colonne
        // dans une unique closure Schema::table. On split en deux opérations
        // et on protège chacune par hasColumn.
        if (Schema::hasColumn('profils', 'image')) {
            Schema::table('profils', function (Blueprint $table) {
                $table->dropColumn('image');
            });
        }

        Schema::table('profils', function (Blueprint $table) {
            $table->string('image')->nullable()->default('images/user.png');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropColumns('profils', ['image']);
    }
};
