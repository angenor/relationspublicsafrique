<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Projet;
use App\Models\ProjetTemoignage;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjetTemoignage>
 */
class ProjetTemoignageFactory extends Factory
{
    protected $model = ProjetTemoignage::class;

    public function definition(): array
    {
        return [
            'projet_id' => Projet::factory(),
            'auteur' => $this->faker->name(),
            'fonction' => $this->faker->optional()->jobTitle(),
            'organisation' => $this->faker->optional()->company(),
            'contenu' => $this->faker->paragraph(),
            'photo' => null,
            'position' => 0,
        ];
    }
}
