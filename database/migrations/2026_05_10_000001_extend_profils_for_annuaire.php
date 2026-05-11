<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('profils', function (Blueprint $table): void {
            $table->string('nationalite', 100)->nullable()->after('prenom');
            $table->string('bio_courte', 280)->nullable()->after('bio');
            $table->text('bio_longue')->nullable()->after('bio_courte');
            $table->string('ville', 150)->nullable()->after('adresse');
            $table->string('organisation', 200)->nullable()->after('ville');

            $table->enum('type_profil', ['expert', 'etudiant', 'alumni', 'partenaire', 'autre'])
                ->default('autre')->after('organisation');
            $table->enum('etat_publication', ['en_attente', 'publie', 'archive'])
                ->default('en_attente')->after('type_profil');

            $table->boolean('masquer_email')->default(true)->after('email');
            $table->boolean('masquer_tel')->default(true)->after('tel');

            $table->string('nom_normalise', 150)->default('')->after('nom');
            $table->string('prenom_normalise', 150)->default('')->after('prenom');
            $table->string('organisation_normalisee', 200)->default('')->after('organisation');
            $table->string('ville_normalisee', 150)->default('')->after('ville');

            $table->boolean('legacy_sans_consentement')->default(false)->after('etat_publication');
            $table->timestamp('published_at')->nullable()->after('legacy_sans_consentement');
            $table->softDeletes();

            $table->index(['etat_publication', 'type_profil'], 'idx_profils_etat_type');
            $table->index('nom_normalise', 'idx_profils_nom_norm');
            $table->index('prenom_normalise', 'idx_profils_prenom_norm');
            $table->index('organisation_normalisee', 'idx_profils_orga_norm');
            $table->index('ville_normalisee', 'idx_profils_ville_norm');
        });
    }

    public function down(): void
    {
        Schema::table('profils', function (Blueprint $table): void {
            $table->dropIndex('idx_profils_etat_type');
            $table->dropIndex('idx_profils_nom_norm');
            $table->dropIndex('idx_profils_prenom_norm');
            $table->dropIndex('idx_profils_orga_norm');
            $table->dropIndex('idx_profils_ville_norm');
            $table->dropSoftDeletes();
            $table->dropColumn([
                'nationalite', 'bio_courte', 'bio_longue', 'ville', 'organisation',
                'type_profil', 'etat_publication', 'masquer_email', 'masquer_tel',
                'nom_normalise', 'prenom_normalise', 'organisation_normalisee', 'ville_normalisee',
                'legacy_sans_consentement', 'published_at',
            ]);
        });
    }
};
