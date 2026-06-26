<?php

declare(strict_types=1);

namespace Tests\Unit\Media;

use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_slug_est_genere_depuis_le_titre(): void
    {
        $media = Media::factory()->create(['titre' => 'Mon Média Génial', 'slug' => null]);

        $this->assertSame('mon-media-genial', $media->slug);
    }

    public function test_le_titre_normalise_est_sans_accents(): void
    {
        $media = Media::factory()->create(['titre' => 'Élection présidentielle à Abidjan']);

        $this->assertSame('election presidentielle a abidjan', $media->titre_normalise);
    }

    public function test_le_temps_de_lecture_est_calcule_depuis_le_contenu(): void
    {
        $content = '<p>'.str_repeat('mot ', 400).'</p>'; // 400 mots → 2 min

        $media = Media::factory()->create(['content' => $content]);

        $this->assertSame(2, $media->reading_time);
    }

    public function test_le_scope_published_exclut_brouillons_programmes_et_futurs(): void
    {
        Media::factory()->create(['status' => 'published', 'published_at' => now()->subDay()]);
        Media::factory()->create(['status' => 'draft', 'published_at' => now()->subDay()]);
        Media::factory()->create(['status' => 'scheduled', 'published_at' => now()->addDay()]);
        Media::factory()->create(['status' => 'published', 'published_at' => now()->addDay()]); // futur

        $this->assertSame(1, Media::published()->count());
    }

    public function test_le_scope_recent_ordonne_par_published_at_desc(): void
    {
        $ancien = Media::factory()->published()->create(['published_at' => now()->subDays(10)]);
        $recent = Media::factory()->published()->create(['published_at' => now()->subDay()]);

        $ids = Media::published()->recent()->pluck('id')->all();

        $this->assertSame([$recent->id, $ancien->id], $ids);
    }

    public function test_le_scope_popular_ordonne_par_popularity_score(): void
    {
        Media::factory()->published()->create(['popularity_score' => 10]);
        $star = Media::factory()->published()->create(['popularity_score' => 500]);

        $this->assertSame($star->id, Media::published()->popular()->first()->id);
    }

    public function test_le_scope_recommended_priorise_pinned_puis_featured(): void
    {
        Media::factory()->published()->create(['popularity_score' => 999, 'featured' => false, 'is_pinned' => false]);
        $epingle = Media::factory()->published()->create(['is_pinned' => true, 'popularity_score' => 1]);

        $this->assertSame($epingle->id, Media::published()->recommended()->first()->id);
    }

    public function test_les_accessors_humanises(): void
    {
        $media = Media::factory()->create([
            'titre' => 'Test Accessor',
            'slug' => 'test-accessor',
            'content' => str_repeat('mot ', 600), // 600 mots → 3 min
            'duration' => 125, // 2:05
        ]);

        $this->assertSame('3 min de lecture', $media->readingTimeHuman);
        $this->assertSame('2:05', $media->durationHuman);
    }
}
