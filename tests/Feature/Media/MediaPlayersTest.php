<?php

declare(strict_types=1);

namespace Tests\Feature\Media;

use App\Models\Media;
use App\Models\MediaSerie;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaPlayersTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_lecteur_audio_avec_soustitres_et_telechargement(): void
    {
        $media = Media::factory()->published()->podcast()->create([
            'titre' => 'Podcast telechargeable',
            'slug' => 'podcast-telechargeable',
            'subtitles_path' => 'media/subtitles/ep.vtt',
            'audio_downloadable' => true,
        ]);

        $this->get($media->link)
            ->assertOk()
            ->assertSee('media-audio-player', false)
            ->assertSee('<track', false)
            ->assertSee('Télécharger', false);
    }

    public function test_le_bouton_telechargement_est_absent_si_non_autorise(): void
    {
        $media = Media::factory()->published()->podcast()->create([
            'slug' => 'podcast-non-telechargeable',
            'audio_downloadable' => false,
        ]);

        $this->get($media->link)
            ->assertOk()
            ->assertSee('media-audio-player', false)
            ->assertDontSee('Télécharger', false);
    }

    public function test_la_video_est_embarquee_en_iframe_responsive(): void
    {
        $media = Media::factory()->published()->video()->create([
            'titre' => 'Video embarquee',
            'slug' => 'video-embarquee',
            'embed_url' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
        ]);

        $this->get($media->link)
            ->assertOk()
            ->assertSee('media-embed', false)
            ->assertSee('youtube-nocookie.com/embed/dQw4w9WgXcQ', false);
    }

    public function test_la_page_serie_liste_les_episodes_dans_l_ordre(): void
    {
        $serie = MediaSerie::factory()->create(['type' => 'podcast']);

        Media::factory()->published()->create(['serie_id' => $serie->id, 'saison' => 1, 'episode' => 2, 'titre' => 'Episode Deux']);
        Media::factory()->published()->create(['serie_id' => $serie->id, 'saison' => 1, 'episode' => 1, 'titre' => 'Episode Un']);
        Media::factory()->published()->create(['serie_id' => $serie->id, 'saison' => 1, 'episode' => 3, 'titre' => 'Episode Trois']);

        $this->get('/media/series/'.$serie->slug)
            ->assertOk()
            ->assertSeeInOrder(['Episode Un', 'Episode Deux', 'Episode Trois']);
    }

    public function test_la_navigation_de_serie_relie_les_episodes(): void
    {
        $serie = MediaSerie::factory()->create(['type' => 'podcast']);
        $ep1 = Media::factory()->published()->create(['serie_id' => $serie->id, 'saison' => 1, 'episode' => 1, 'titre' => 'Premier Episode']);
        $ep2 = Media::factory()->published()->create(['serie_id' => $serie->id, 'saison' => 1, 'episode' => 2, 'titre' => 'Deuxieme Episode']);

        // Depuis l'épisode 1, l'épisode suivant (2) doit apparaître dans la navigation.
        $this->get($ep1->link)
            ->assertOk()
            ->assertSee('Deuxieme Episode', false);
    }
}
