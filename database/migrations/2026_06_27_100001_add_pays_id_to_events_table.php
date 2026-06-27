<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * CORRECTIF (research R2) : la colonne `events.pays_id` était référencée par
 * EventResource (form/table/filtre) et la relation Event::pays(), mais aucune
 * migration ne la créait → erreurs SQL latentes et filtre « par pays » (FR-015)
 * inopérant. FK *logique* (colonne integer, sans contrainte DB) car la table
 * legacy `pays` a un `id` int sans AUTO_INCREMENT (même contournement que 002/003).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            if (! Schema::hasColumn('events', 'pays_id')) {
                $table->integer('pays_id')->unsigned()->nullable()->after('category_id');
                $table->index('pays_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            if (Schema::hasColumn('events', 'pays_id')) {
                $table->dropIndex(['pays_id']);
                $table->dropColumn('pays_id');
            }
        });
    }
};
