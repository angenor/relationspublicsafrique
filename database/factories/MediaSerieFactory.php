<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\MediaSerie;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MediaSerie>
 */
class MediaSerieFactory extends Factory
{
    protected $model = MediaSerie::class;

    public function definition(): array
    {
        $titre = Str::ucfirst($this->faker->unique()->words(3, true));

        return [
            'titre' => $titre,
            // slug + titre_normalise gérés par MediaSerie::booted().
            'description' => $this->faker->optional()->paragraph(),
            'type' => $this->faker->randomElement(array_keys(MediaSerie::$types)),
            'online' => true,
            'position' => 0,
        ];
    }

    public function offline(): static
    {
        return $this->state(fn () => ['online' => false]);
    }
}
