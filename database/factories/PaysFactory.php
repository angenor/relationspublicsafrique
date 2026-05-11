<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pays>
 */
class PaysFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->unique()->country();

        return [
            'name' => $name,
            'slug' => Str::slug($name).'-'.Str::random(4),
            'online' => 1,
            'indicatif' => '+'.$this->faker->numberBetween(1, 999),
        ];
    }

    public function senegal(): static
    {
        return $this->state(fn () => [
            'name' => 'Sénégal',
            'slug' => 'senegal',
            'indicatif' => '+221',
        ]);
    }

    public function coteDivoire(): static
    {
        return $this->state(fn () => [
            'name' => "Côte d'Ivoire",
            'slug' => 'cote-divoire',
            'indicatif' => '+225',
        ]);
    }
}
