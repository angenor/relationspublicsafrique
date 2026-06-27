<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Livewire\Events\GrilleEvents;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_page_listing_repond_200(): void
    {
        Event::factory()->upcoming()->create(['title' => 'Événement visible']);

        $this->get('/evenements')
            ->assertOk()
            ->assertSee('Nos événements', false);
    }

    public function test_evenements_groupes_par_statut_temporel(): void
    {
        Event::factory()->upcoming()->create(['title' => 'Conference a venir']);
        Event::factory()->ongoing()->create(['title' => 'Atelier en cours']);
        Event::factory()->past()->create(['title' => 'Seminaire clos']);

        Livewire::test(GrilleEvents::class)
            ->assertSee('Conference a venir')
            ->assertSee('Atelier en cours')
            ->assertSee('Seminaire clos')
            ->assertSee('En cours')
            ->assertSee('À venir')
            ->assertSee('Clos');
    }

    public function test_un_brouillon_est_exclu_du_listing(): void
    {
        Event::factory()->upcoming()->create(['title' => 'Publie liste']);
        Event::factory()->upcoming()->draft()->create(['title' => 'Brouillon cache']);

        Livewire::test(GrilleEvents::class)
            ->assertSee('Publie liste')
            ->assertDontSee('Brouillon cache');
    }

    public function test_un_presentiel_publie_est_visible(): void
    {
        // Régression R1 : un événement présentiel (online=false) publié DOIT apparaître.
        Event::factory()->upcoming()->presentiel()->create(['title' => 'Presentiel publie', 'location' => 'Lomé']);

        Livewire::test(GrilleEvents::class)
            ->assertSee('Presentiel publie');
    }

    public function test_charger_plus_augmente_la_taille_de_page(): void
    {
        Event::factory()->count(15)->upcoming()->create();

        Livewire::test(GrilleEvents::class)
            ->assertSet('perPage', 12)
            ->call('chargerPlus')
            ->assertSet('perPage', 24);
    }
}
