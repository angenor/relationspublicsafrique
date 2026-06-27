<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Event;
use App\Models\EventSpeaker;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EventSpeaker>
 */
class EventSpeakerFactory extends Factory
{
    protected $model = EventSpeaker::class;

    public function definition(): array
    {
        return [
            'event_id' => Event::factory(),
            'nom' => $this->faker->name(),
            'role' => $this->faker->optional()->jobTitle(),
            'organisation' => $this->faker->optional()->company(),
            'photo' => null,
            'bio' => $this->faker->optional()->sentence(12),
            'position' => 0,
        ];
    }
}
