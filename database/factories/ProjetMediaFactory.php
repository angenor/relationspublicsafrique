<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Projet;
use App\Models\ProjetMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ProjetMedia>
 */
class ProjetMediaFactory extends Factory
{
    protected $model = ProjetMedia::class;

    public function definition(): array
    {
        return [
            'projet_id' => Projet::factory(),
            'type' => 'image',
            'chemin' => 'projets/galerie/demo.jpg',
            'url_embed' => null,
            'legende' => $this->faker->optional()->sentence(6),
            'position' => 0,
        ];
    }

    public function image(): static
    {
        return $this->state(fn () => [
            'type' => 'image',
            'chemin' => 'projets/galerie/demo.jpg',
            'url_embed' => null,
        ]);
    }

    public function video(): static
    {
        return $this->state(fn () => [
            'type' => 'video',
            'chemin' => null,
            'url_embed' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);
    }
}
