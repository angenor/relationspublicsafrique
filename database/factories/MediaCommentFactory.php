<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Media;
use App\Models\MediaComment;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MediaComment>
 */
class MediaCommentFactory extends Factory
{
    protected $model = MediaComment::class;

    public function definition(): array
    {
        return [
            'media_id' => Media::factory(),
            'parent_id' => null,
            'author_name' => $this->faker->name(),
            'author_email' => $this->faker->safeEmail(),
            'body' => $this->faker->paragraph(),
            'status' => 'pending',
            'ip_address' => $this->faker->ipv4(),
        ];
    }

    public function approved(): static
    {
        return $this->state(fn () => ['status' => 'approved']);
    }

    public function rejected(): static
    {
        return $this->state(fn () => ['status' => 'rejected']);
    }
}
