<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Media;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Media>
 */
class MediaFactory extends Factory
{
    protected $model = Media::class;

    public function definition(): array
    {
        $titre = rtrim($this->faker->unique()->sentence(4), '.');

        return [
            'titre' => $titre,
            // slug + titre_normalise + reading_time gérés par MediaObserver.
            'type' => $this->faker->randomElement(array_keys(Media::$types)),
            'chapo' => $this->faker->optional()->sentence(12),
            'content' => $this->faker->paragraphs(3, true),
            'cover_image' => null,
            'status' => 'published',
            'published_at' => now()->subDays($this->faker->numberBetween(1, 30)),
            'view' => $this->faker->numberBetween(0, 500),
            'popularity_score' => $this->faker->numberBetween(0, 500),
            'featured' => false,
            'is_pinned' => false,
            'audio_downloadable' => false,
            'user_id' => User::factory(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn () => ['status' => 'draft', 'published_at' => null]);
    }

    public function scheduled(): static
    {
        return $this->state(fn () => ['status' => 'scheduled', 'published_at' => now()->addDays(7)]);
    }

    public function published(): static
    {
        return $this->state(fn () => ['status' => 'published', 'published_at' => now()->subDay()]);
    }

    public function archived(): static
    {
        return $this->state(fn () => ['status' => 'archived']);
    }

    public function featured(): static
    {
        return $this->state(fn () => ['featured' => true]);
    }

    public function pinned(int $position = 1): static
    {
        return $this->state(fn () => ['is_pinned' => true, 'pinned_position' => $position]);
    }

    public function ofType(string $type): static
    {
        return $this->state(fn () => ['type' => $type]);
    }

    public function podcast(): static
    {
        return $this->state(fn () => [
            'type' => 'podcast',
            'media_kind' => 'audio',
            'audio_file' => 'media/audio/demo.mp3',
            'duration' => $this->faker->numberBetween(600, 3600),
        ]);
    }

    public function video(): static
    {
        return $this->state(fn () => [
            'type' => 'video',
            'media_kind' => 'youtube',
            'embed_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'duration' => $this->faker->numberBetween(120, 1800),
        ]);
    }
}
