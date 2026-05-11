<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\DomaineExpertise;
use App\Models\HistoriqueProfil;
use App\Models\LienExterne;
use App\Models\Profil;
use App\Services\Annuaire\TextNormalizer;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class AnnuaireMigrationSeeder extends Seeder
{
    /**
     * Migration idempotente des profils legacy vers le nouveau schéma annuaire.
     */
    public function run(): void
    {
        Profil::withTrashed()->chunkById(200, function ($profils): void {
            foreach ($profils as $profil) {
                $this->migrerProfil($profil);
            }
        });
    }

    private function migrerProfil(Profil $profil): void
    {
        $alreadyMigrated = HistoriqueProfil::where('profil_id', $profil->id)
            ->where('action', 'migration_initiale')
            ->exists();

        if ($alreadyMigrated) {
            return;
        }

        $etat = ((int) $profil->online === 1 && (int) $profil->aprouve === 1) ? 'publie' : 'en_attente';

        $profil->forceFill([
            'etat_publication' => $etat,
            'bio_longue' => $profil->bio_longue ?: $profil->bio,
            'type_profil' => $profil->type_profil ?: 'autre',
            'legacy_sans_consentement' => true,
            'nom_normalise' => TextNormalizer::normalize($profil->nom),
            'prenom_normalise' => TextNormalizer::normalize($profil->prenom),
            'organisation_normalisee' => TextNormalizer::normalize($profil->organisation),
            'ville_normalisee' => TextNormalizer::normalize($profil->ville),
            'published_at' => $etat === 'publie' ? ($profil->published_at ?: $profil->created_at) : null,
        ])->saveQuietly();

        $this->migrerDomaines($profil);
        $this->migrerLiens($profil);

        HistoriqueProfil::create([
            'profil_id' => $profil->id,
            'user_id' => null,
            'action' => 'migration_initiale',
            'diff' => ['etat_publication' => ['avant' => null, 'apres' => $etat]],
            'motif' => 'Migration initiale du schéma annuaire',
            'ip' => null,
            'user_agent' => 'AnnuaireMigrationSeeder',
            'created_at' => Carbon::now(),
        ]);
    }

    private function migrerDomaines(Profil $profil): void
    {
        $raw = (string) ($profil->domaine ?? '');
        if ($raw === '') {
            return;
        }

        $libelles = preg_split('/[,;]/', $raw) ?: [];
        $ids = [];
        foreach ($libelles as $libelle) {
            $libelle = trim($libelle);
            if ($libelle === '') {
                continue;
            }
            $slug = Str::slug($libelle);
            $domaine = DomaineExpertise::firstOrCreate(
                ['slug' => $slug],
                ['libelle' => $libelle]
            );
            $ids[] = $domaine->id;
        }

        if (!empty($ids)) {
            $profil->domainesExpertise()->syncWithoutDetaching($ids);
        }
    }

    private function migrerLiens(Profil $profil): void
    {
        $mapping = [
            'facebook' => 'facebook',
            'twitter' => 'twitter',
            'youtube' => 'youtube',
            'linkding' => 'linkedin',
            'site' => 'site_web',
        ];

        foreach ($mapping as $legacyField => $type) {
            $url = $profil->{$legacyField} ?? null;
            if (!is_string($url) || $url === '') {
                continue;
            }

            $exists = LienExterne::where('profil_id', $profil->id)
                ->where('type', $type)
                ->where('url', $url)
                ->exists();

            if (!$exists) {
                LienExterne::create([
                    'profil_id' => $profil->id,
                    'type' => $type,
                    'url' => $url,
                ]);
            }
        }
    }
}
