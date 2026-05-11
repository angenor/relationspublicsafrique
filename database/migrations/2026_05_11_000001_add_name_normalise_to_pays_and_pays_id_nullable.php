<?php

declare(strict_types=1);

use App\Models\Pays;
use App\Services\Annuaire\TextNormalizer;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ajout d'une colonne normalisée pour la recherche sur pays.name
        //    (résout AnnuaireSearchTest::test_recherche_libre_sur_pays_via_relation
        //    où "senegal" doit matcher "Sénégal").
        if (! Schema::hasColumn('pays', 'name_normalise')) {
            Schema::table('pays', function (Blueprint $table): void {
                $table->string('name_normalise', 200)->nullable()->after('name');
                $table->index('name_normalise', 'idx_pays_name_norm');
            });
        }

        // Backfill de name_normalise sur les lignes existantes.
        foreach (Pays::query()->cursor() as $pays) {
            $pays->name_normalise = TextNormalizer::normalize($pays->name);
            $pays->saveQuietly();
        }

        // 2. Rendre profils.pays_id nullable + restaurer les defaults legacy
        //    sur online/aprouve (perdus dans la BD MySQL existante).
        //    Approche driver-spécifique car doctrine/dbal n'est pas installé.
        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            DB::statement('ALTER TABLE profils MODIFY pays_id BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE profils MODIFY user_id BIGINT UNSIGNED NULL');
            DB::statement('ALTER TABLE profils MODIFY online INT NOT NULL DEFAULT 0');
            DB::statement('ALTER TABLE profils MODIFY aprouve INT NOT NULL DEFAULT 0');
        }
        // Pour SQLite (tests in-memory), la migration originale est patchée
        // pour créer pays_id nullable dès la création (voir
        // 2023_04_27_145241_create_profils_table.php). Les defaults online/aprouve
        // sont déjà présents dans la migration originale.
    }

    public function down(): void
    {
        if (Schema::hasColumn('pays', 'name_normalise')) {
            Schema::table('pays', function (Blueprint $table): void {
                $table->dropIndex('idx_pays_name_norm');
                $table->dropColumn('name_normalise');
            });
        }

        $driver = DB::connection()->getDriverName();
        if ($driver === 'mysql' || $driver === 'mariadb') {
            // Restauration NOT NULL : seulement si plus aucune ligne null.
            $hasNulls = DB::table('profils')->whereNull('pays_id')->exists();
            if (! $hasNulls) {
                DB::statement('ALTER TABLE profils MODIFY pays_id BIGINT UNSIGNED NOT NULL');
            }
        }
    }
};
