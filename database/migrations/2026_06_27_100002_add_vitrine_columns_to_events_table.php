<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Enrichissement vitrine de la table `events` (feature 004-evenements) :
 * contenu détaillé (objectifs/programme/public cible/compte rendu) et mode
 * d'inscription interne|externe (research R9). Additif et idempotent.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            if (! Schema::hasColumn('events', 'objectifs')) {
                $table->text('objectifs')->nullable()->after('description');
            }
            if (! Schema::hasColumn('events', 'programme')) {
                $table->longText('programme')->nullable()->after('objectifs');
            }
            if (! Schema::hasColumn('events', 'public_cible')) {
                $table->text('public_cible')->nullable()->after('programme');
            }
            if (! Schema::hasColumn('events', 'compte_rendu')) {
                $table->longText('compte_rendu')->nullable()->after('public_cible');
            }
            if (! Schema::hasColumn('events', 'registration_mode')) {
                $table->string('registration_mode', 20)->default('internal')->after('online');
                $table->index('registration_mode');
            }
            if (! Schema::hasColumn('events', 'registration_url')) {
                $table->string('registration_url')->nullable()->after('registration_mode');
            }
        });
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            if (Schema::hasColumn('events', 'registration_mode')) {
                $table->dropIndex(['registration_mode']);
            }
            foreach (['objectifs', 'programme', 'public_cible', 'compte_rendu', 'registration_mode', 'registration_url'] as $column) {
                if (Schema::hasColumn('events', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
