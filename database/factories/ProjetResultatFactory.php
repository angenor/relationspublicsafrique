<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Projet;
use App\Models\ProjetResultat;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjetResultat>
 */
class ProjetResultatFactory extends Factory
{
    protected $model = ProjetResultat::class;

    public function definition(): array
    {
        return [
            'projet_id' => Projet::factory(),
            'libelle' => $this->faker->randomElement(['Bénéficiaires formés', 'Pays couverts', 'Ateliers organisés', 'Partenaires mobilisés']),
            'valeur' => (string) $this->faker->numberBetween(5, 5000),
            'unite' => $this->faker->randomElement([null, 'personnes', 'pays', 'ateliers']),
            'icone' => null,
            'position' => 0,
        ];
    }
}
