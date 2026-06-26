<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Media;
use Illuminate\Console\Command;

class MediaRecomputePopularity extends Command
{
    protected $signature = 'media:recompute-popularity';

    protected $description = 'Recalcule popularity_score = vues pondérées par la fraîcheur (décroissance exponentielle).';

    public function handle(): int
    {
        $halfLife = max(1, (int) config('media.popularity_half_life_days', 14));
        $count = 0;

        Media::query()
            ->whereNotNull('published_at')
            ->cursor()
            ->each(function (Media $media) use ($halfLife, &$count): void {
                $age = abs(now()->diffInDays($media->published_at));
                $factor = pow(0.5, $age / $halfLife);
                $media->popularity_score = (int) round(((int) $media->view) * $factor);
                $media->saveQuietly();
                $count++;
            });

        $this->info("Scores de popularité recalculés : {$count}");

        return self::SUCCESS;
    }
}
