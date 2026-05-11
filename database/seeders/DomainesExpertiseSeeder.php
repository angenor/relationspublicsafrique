<?php

namespace Database\Seeders;

use App\Models\DomaineExpertise;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DomainesExpertiseSeeder extends Seeder
{
    public function run(): void
    {
        $domaines = [
            'Communication',
            'Communication politique',
            'Communication de crise',
            'Communication digitale',
            'Lobbying',
            'Affaires publiques',
            'Relations presse',
            'Relations médias',
            'Événementiel',
            'Relations internationales',
            'Diplomatie',
            'Marketing d\'influence',
            'Stratégie de marque',
            'Marque employeur',
            'Études et sondages',
            'Audit et conseil',
            'RSE et développement durable',
            'Plaidoyer',
            'Édition et publication',
            'Formation et coaching',
        ];

        foreach ($domaines as $libelle) {
            DomaineExpertise::query()->updateOrCreate(
                ['slug' => Str::slug($libelle)],
                ['libelle' => $libelle]
            );
        }
    }
}
