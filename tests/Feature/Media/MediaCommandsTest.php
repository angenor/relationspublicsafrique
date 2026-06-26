<?php

declare(strict_types=1);

namespace Tests\Feature\Media;

use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaCommandsTest extends TestCase
{
    use RefreshDatabase;

    public function test_publish_scheduled_publie_les_contenus_echus(): void
    {
        $echu = Media::factory()->create(['status' => 'scheduled', 'published_at' => now()->subMinute()]);
        $futur = Media::factory()->create(['status' => 'scheduled', 'published_at' => now()->addDay()]);

        $this->artisan('media:publish-scheduled')->assertSuccessful();

        $this->assertSame('published', $echu->fresh()->status);
        $this->assertSame('scheduled', $futur->fresh()->status);
        $this->assertSame(1, Media::published()->count());
    }

    public function test_recompute_popularity_pondere_par_la_fraicheur(): void
    {
        $recent = Media::factory()->published()->create(['view' => 100, 'published_at' => now()->subDay()]);
        $vieux = Media::factory()->published()->create(['view' => 100, 'published_at' => now()->subDays(60)]);

        $this->artisan('media:recompute-popularity')->assertSuccessful();

        $this->assertGreaterThan(
            (int) $vieux->fresh()->popularity_score,
            (int) $recent->fresh()->popularity_score,
        );
    }
}
