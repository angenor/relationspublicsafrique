<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Profil;
use App\Services\Annuaire\TextNormalizer;
use Illuminate\Console\Command;

class AnnuaireRenormaliser extends Command
{
    protected $signature = 'annuaire:renormaliser';

    protected $description = 'Recalcule les colonnes *_normalise pour tous les profils.';

    public function handle(): int
    {
        $count = 0;
        Profil::withTrashed()->chunkById(500, function ($profils) use (&$count): void {
            foreach ($profils as $profil) {
                $profil->forceFill([
                    'nom_normalise' => TextNormalizer::normalize($profil->nom),
                    'prenom_normalise' => TextNormalizer::normalize($profil->prenom),
                    'organisation_normalisee' => TextNormalizer::normalize($profil->organisation),
                    'ville_normalisee' => TextNormalizer::normalize($profil->ville),
                ])->saveQuietly();
                $count++;
            }
        });

        $this->info("Renormalisation terminée : {$count} profils.");

        return self::SUCCESS;
    }
}
