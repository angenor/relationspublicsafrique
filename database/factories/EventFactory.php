<?php

namespace Database\Factories;

use App\Models\Event;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startDate = now()->addDays(rand(7, 180));
        $endDate = $startDate->copy()->addHours(rand(2, 8));
        $registrationDeadline = $startDate->copy()->subDays(rand(1, 7));

        return [
            'title' => $this->faker->sentence(4),
            'slug' => fn(array $attributes) => Str::slug($attributes['title']),
            'description' => $this->faker->paragraphs(3, true),
            'resume' => $this->faker->paragraph(),
            'image' => null,
            'location' => $this->faker->city(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'registration_deadline' => $registrationDeadline,
            'max_participants' => $this->faker->optional(0.7)->numberBetween(10, 200),
            'current_participants' => $this->faker->numberBetween(0, 50),
            'price' => $this->faker->randomFloat(2, 0, 500),
            'status' => $this->faker->randomElement(['draft', 'published', 'published', 'published']),
            'is_featured' => $this->faker->boolean(20),
            'online' => $this->faker->boolean(80),
            'user_id' => User::first()?->id ?? 1,
            'category_id' => Category::first()?->id ?? 1,
        ];
    }

    /**
     * Indicate that the event is published.
     */
    public function published(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => 'published',
            'online' => true,
        ]);
    }

    /**
     * Indicate that the event is featured.
     */
    public function featured(): static
    {
        return $this->state(fn(array $attributes) => [
            'is_featured' => true,
            'status' => 'published',
            'online' => true,
        ]);
    }

    /**
     * Indicate that the event is upcoming.
     */
    public function upcoming(): static
    {
        return $this->state(fn(array $attributes) => [
            'start_date' => $this->faker->dateTimeBetween('+1 week', '+6 months'),
            'status' => 'published',
            'online' => true,
        ]);
    }
}
