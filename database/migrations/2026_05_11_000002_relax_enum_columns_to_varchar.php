<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Aligne les colonnes ENUM legacy `demandes_moderation.decision` et
 * `historique_profils.action` sur les valeurs réellement utilisées par les
 * services (`soumis`, `approuve`, `rejete`, `archive`, `consentement_atteste`,
 * `soumis_moderation`, etc.) en passant en VARCHAR.
 *
 * Sans ce passage en VARCHAR, MySQL tronque l'insertion (`SQLSTATE[01000]:
 * Data truncated for column 'decision'`) ou refuse la valeur côté CHECK.
 * Les nouveaux environnements créent déjà ces colonnes en VARCHAR via les
 * migrations 2026_05_10_000007 et 2026_05_10_000008 (patchées) ; ce fichier
 * existe uniquement pour rattraper les bases déjà migrées.
 */
return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();
        if ($driver !== 'mysql' && $driver !== 'mariadb') {
            return;
        }

        DB::statement("ALTER TABLE demandes_moderation MODIFY decision VARCHAR(20) NOT NULL DEFAULT 'soumis'");
        DB::statement('ALTER TABLE historique_profils MODIFY action VARCHAR(40) NOT NULL');
    }

    public function down(): void
    {
        // Pas de rollback automatique : restaurer un ENUM ferait perdre les
        // valeurs comme `archive`, `consentement_atteste`, etc. Migration à
        // considérer comme one-way.
    }
};
