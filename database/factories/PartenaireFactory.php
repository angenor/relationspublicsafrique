<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Partenaire;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Partenaire>
 */
class PartenaireFactory extends Factory
{
    protected $model = Partenaire::class;

    public function definition(): array
    {
        $nom = $this->faker->unique()->company();

        return [
            'nom' => $nom,
            'slug' => Str::slug($nom).'-'.$this->faker->unique()->numberBetween(1, 99999),
            'logo' => null,
            'url' => $this->faker->optional()->url(),
        ];
    }
}
