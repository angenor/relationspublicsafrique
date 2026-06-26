<?php

declare(strict_types=1);

namespace Tests\Feature\Media;

use App\Livewire\Media\GrilleMedias;
use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class MediaExplorerTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_recherche_restreint_le_jeu(): void
    {
        Media::factory()->published()->create(['titre' => 'Communication politique africaine']);
        Media::factory()->published()->create(['titre' => 'Veille numerique hebdomadaire']);

        Livewire::test(GrilleMedias::class)
            ->set('q', 'politique')
            ->assertSee('Communication politique africaine')
            ->assertDontSee('Veille numerique hebdomadaire');
    }

    public function test_le_filtre_type_restreint_le_jeu(): void
    {
        Media::factory()->published()->ofType('podcast')->create(['titre' => 'Podcast Singulier']);
        Media::factory()->published()->ofType('article')->create(['titre' => 'Article Singulier']);

        Livewire::test(GrilleMedias::class)
            ->set('type', 'podcast')
            ->assertSee('Podcast Singulier')
            ->assertDontSee('Article Singulier');
    }

    public function test_le_tri_populaire_change_l_ordre(): void
    {
        $faible = Media::factory()->published()->create(['titre' => 'Peu populaire', 'popularity_score' => 1]);
        $fort = Media::factory()->published()->create(['titre' => 'Tres populaire', 'popularity_score' => 999]);

        Livewire::test(GrilleMedias::class)
            ->set('tri', 'populaire')
            ->assertSeeInOrder(['Tres populaire', 'Peu populaire']);
    }

    public function test_charger_plus_augmente_le_nombre_d_items(): void
    {
        Media::factory()->published()->count(15)->create();

        Livewire::test(GrilleMedias::class)
            ->assertSee('Charger plus')
            ->call('loadMore')
            ->assertSet('perPage', 24)
            ->assertDontSee('Charger plus');
    }

    public function test_l_etat_est_initialise_depuis_l_url(): void
    {
        Media::factory()->published()->ofType('podcast')->create(['titre' => 'Initialise Depuis Url']);
        Media::factory()->published()->ofType('article')->create(['titre' => 'Ne Doit Pas Apparaitre']);

        Livewire::withQueryParams(['type' => 'podcast'])
            ->test(GrilleMedias::class)
            ->assertSet('type', 'podcast')
            ->assertSee('Initialise Depuis Url')
            ->assertDontSee('Ne Doit Pas Apparaitre');
    }

    public function test_reset_filters_efface_les_criteres(): void
    {
        Livewire::test(GrilleMedias::class)
            ->set('q', 'quelque chose')
            ->set('type', 'podcast')
            ->call('resetFilters')
            ->assertSet('q', '')
            ->assertSet('type', '');
    }
}
