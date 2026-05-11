<?php

namespace Database\Seeders;

use App\Models\DomaineExpertise;
use App\Models\Pays;
use App\Models\Profil;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Seeder de stress : génère 5 000 profils factices afin de mesurer
 * les SC-002 (95 % recherches < 1 s) et SC-008 (recherche + 3 filtres + tri < 1,5 s).
 *
 * Idempotent : ne ré-insère pas si 5000+ profils stress existent déjà.
 * Marqueur : tous les profils créés ont un slug préfixé `stress-`.
 */
class ProfilsStressSeeder extends Seeder
{
    public const COUNT_DEFAULT = 5000;

    public function run(?int $count = null): void
    {
        $count = $count ?? self::COUNT_DEFAULT;

        $existants = Profil::query()->where('slug', 'like', 'stress-%')->count();
        if ($existants >= $count) {
            $this->command?->info("Stress seeder : déjà {$existants} profils 'stress-*' en base, skip.");

            return;
        }

        $aCreer = $count - $existants;
        $this->command?->info("Stress seeder : génération de {$aCreer} profils factices…");

        // Assurer un minimum de référentiels.
        if (Pays::count() < 5) {
            Pays::factory()->count(20)->create();
        }
        if (DomaineExpertise::count() < 5) {
            (new DomainesExpertiseSeeder)->run();
        }
        if (Tag::count() < 5) {
            foreach (['afrique', 'francophonie', 'jeunesse', 'média', 'durable'] as $t) {
                Tag::query()->updateOrCreate(
                    ['slug' => Str::slug($t)],
                    ['libelle' => $t]
                );
            }
        }

        $userSystem = User::query()->where('type', 'admin')->first()
            ?? User::factory()->create(['type' => 'admin', 'name' => 'Stress System']);

        $paysIds = Pays::query()->pluck('id')->all();
        $domaineIds = DomaineExpertise::query()->pluck('id')->all();
        $tagIds = Tag::query()->pluck('id')->all();
        $types = ['expert', 'etudiant', 'alumni', 'partenaire', 'autre'];

        $batchSize = 250;
        $now = Carbon::now();

        for ($i = 0; $i < $aCreer; $i += $batchSize) {
            $batch = [];
            $limit = min($batchSize, $aCreer - $i);

            for ($j = 0; $j < $limit; $j++) {
                $seq = $existants + $i + $j + 1;
                $batch[] = $this->buildProfil($seq, $paysIds, $userSystem->id, $types, $now);
            }

            Profil::query()->insert($batch);
        }

        // Attache 1-3 domaines et 0-2 tags à un échantillon (~20 %) pour éviter
        // une explosion d'inserts pivot tout en gardant des filtres représentatifs.
        $sampleSize = (int) round($aCreer * 0.2);
        $profils = Profil::query()
            ->where('slug', 'like', 'stress-%')
            ->orderByDesc('id')
            ->limit($sampleSize)
            ->get(['id']);

        foreach ($profils as $p) {
            if (! empty($domaineIds)) {
                $picked = $this->randomPick($domaineIds, random_int(1, 3));
                $p->domainesExpertise()->syncWithoutDetaching($picked);
            }
            if (! empty($tagIds) && random_int(0, 1) === 1) {
                $picked = $this->randomPick($tagIds, random_int(1, 2));
                $p->tags()->syncWithoutDetaching($picked);
            }
        }

        $this->command?->info("Stress seeder : OK ({$aCreer} profils insérés).");
    }

    private function buildProfil(int $seq, array $paysIds, int $userId, array $types, Carbon $now): array
    {
        $nom = 'StressNom'.$seq;
        $prenom = 'StressPrenom'.$seq;
        $orga = 'Organisation'.($seq % 200);
        $ville = 'Ville'.($seq % 50);

        return [
            'name' => $prenom.' '.$nom,
            'slug' => 'stress-'.$seq.'-'.Str::random(6),
            'nom' => $nom,
            'prenom' => $prenom,
            'fonction' => 'Fonction'.($seq % 30),
            'domaine' => 'communication',
            'bio' => 'Bio stress profil '.$seq,
            'bio_courte' => 'Bio courte '.$seq,
            'bio_longue' => 'Bio longue stress profil '.$seq,
            'ville' => $ville,
            'organisation' => $orga,
            'nationalite' => 'Sénégalaise',
            'type_profil' => $types[$seq % count($types)],
            'etat_publication' => 'publie',
            'masquer_email' => $seq % 7 === 0,
            'masquer_tel' => $seq % 5 === 0,
            'nom_normalise' => mb_strtolower($nom),
            'prenom_normalise' => mb_strtolower($prenom),
            'organisation_normalisee' => mb_strtolower($orga),
            'ville_normalisee' => mb_strtolower($ville),
            'legacy_sans_consentement' => false,
            'published_at' => $now,
            'online' => 1,
            'aprouve' => 1,
            'email' => 'stress'.$seq.'@example.test',
            'tel' => '+221700000'.str_pad((string) ($seq % 1000), 3, '0', STR_PAD_LEFT),
            'pays_id' => $paysIds[$seq % count($paysIds)] ?? null,
            'user_id' => $userId,
            'created_at' => $now,
            'updated_at' => $now,
        ];
    }

    private function randomPick(array $items, int $n): array
    {
        if ($n >= count($items)) {
            return $items;
        }
        $keys = array_rand($items, $n);
        if (! is_array($keys)) {
            $keys = [$keys];
        }

        return array_intersect_key($items, array_flip($keys));
    }
}
