<?php

namespace Database\Factories;

use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 *
 * Note (research R1) : `online` représente désormais le FORMAT (présentiel/en
 * ligne) et n'est plus un gate de visibilité — les états `published()`/`featured()`
 * ne le forcent donc plus.
 */
class EventFactory extends Factory
{
    public function definition(): array
    {
        $startDate = now()->addDays(rand(7, 180));
        $endDate = $startDate->copy()->addHours(rand(2, 8));
        $registrationDeadline = $startDate->copy()->subDays(rand(1, 7));

        return [
            'title' => $this->faker->sentence(4),
            'slug' => fn (array $attributes) => Str::slug($attributes['title']),
            'description' => $this->faker->paragraphs(3, true),
            'resume' => $this->faker->paragraph(),
            'objectifs' => $this->faker->optional()->paragraph(),
            'programme' => $this->faker->optional()->paragraphs(2, true),
            'public_cible' => $this->faker->optional()->sentence(10),
            'image' => null,
            'location' => $this->faker->city(),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'registration_deadline' => $registrationDeadline,
            'max_participants' => $this->faker->optional(0.7)->numberBetween(10, 200),
            'current_participants' => $this->faker->numberBetween(0, 9),
            'price' => $this->faker->randomFloat(2, 0, 500),
            'status' => $this->faker->randomElement(['draft', 'published', 'published', 'published']),
            'is_featured' => $this->faker->boolean(20),
            'online' => $this->faker->boolean(40),
            'registration_mode' => 'internal',
            'registration_url' => null,
            'user_id' => User::first()?->id ?? 1,
            'category_id' => Category::first()?->id ?? 1,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'cancelled',
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
            'status' => 'published',
        ]);
    }

    /** Événement à venir (publié). */
    public function upcoming(): static
    {
        $start = now()->addDays(rand(7, 120));

        return $this->state(fn (array $attributes) => [
            'start_date' => $start,
            'end_date' => (clone $start)->addHours(rand(2, 8)),
            'registration_deadline' => (clone $start)->subDays(2),
            'status' => 'published',
        ]);
    }

    /** Événement en cours (démarré, pas encore terminé). */
    public function ongoing(): static
    {
        return $this->state(fn (array $attributes) => [
            'start_date' => now()->subHours(2),
            'end_date' => now()->addHours(2),
            'status' => 'published',
        ]);
    }

    /** Événement clos (terminé). */
    public function past(): static
    {
        $start = now()->subDays(rand(10, 120));

        return $this->state(fn (array $attributes) => [
            'start_date' => $start,
            'end_date' => (clone $start)->addHours(rand(2, 8)),
            'registration_deadline' => (clone $start)->subDays(2),
            'status' => 'published',
        ]);
    }

    /** Inscription via lien externe. */
    public function external(string $url = 'https://exemple.org/inscription'): static
    {
        return $this->state(fn (array $attributes) => [
            'registration_mode' => 'external',
            'registration_url' => $url,
        ]);
    }

    /** Format en ligne. */
    public function online(): static
    {
        return $this->state(fn (array $attributes) => [
            'online' => true,
        ]);
    }

    /** Format présentiel. */
    public function presentiel(): static
    {
        return $this->state(fn (array $attributes) => [
            'online' => false,
        ]);
    }
}
