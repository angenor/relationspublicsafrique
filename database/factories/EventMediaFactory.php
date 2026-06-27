<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventMedia;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventMedia>
 */
class EventMediaFactory extends Factory
{
    protected $model = EventMedia::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'type' => 'image',
            'chemin' => 'events/medias/demo.jpg',
            'url_embed' => null,
            'legende' => $this->faker->optional()->sentence(6),
            'position' => 0,
        ];
    }

    public function image(): static
    {
        return $this->state(fn () => [
            'type' => 'image',
            'chemin' => 'events/medias/demo.jpg',
            'url_embed' => null,
        ]);
    }

    public function replay(): static
    {
        return $this->state(fn () => [
            'type' => 'replay',
            'chemin' => null,
            'url_embed' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);
    }

    public function document(): static
    {
        return $this->state(fn () => [
            'type' => 'document',
            'chemin' => 'events/medias/compte-rendu.pdf',
            'url_embed' => null,
        ]);
    }
}
