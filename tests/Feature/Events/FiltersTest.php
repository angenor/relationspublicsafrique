<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Livewire\Events\GrilleEvents;
use App\Models\Category;
use App\Models\Event;
use App\Models\Pays;
use App\Services\Events\EventSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_filtre_par_statut_temporel(): void
    {
        Event::factory()->upcoming()->create(['title' => 'Evenement a venir']);
        Event::factory()->past()->create(['title' => 'Evenement passe']);

        Livewire::test(GrilleEvents::class)
            ->set('statut', 'past')
            ->assertSee('Evenement passe')
            ->assertDontSee('Evenement a venir');

        Livewire::test(GrilleEvents::class)
            ->set('statut', 'upcoming')
            ->assertSee('Evenement a venir')
            ->assertDontSee('Evenement passe');
    }

    public function test_filtre_par_type(): void
    {
        $cat = Category::factory()->create(['type' => 'event', 'name' => 'Atelier', 'slug' => 'atelier-f', 'online' => 1]);
        Event::factory()->upcoming()->create(['title' => 'Dans le type', 'category_id' => $cat->id]);
        Event::factory()->upcoming()->create(['title' => 'Hors du type', 'category_id' => 9999]);

        Livewire::test(GrilleEvents::class)
            ->set('type', (string) $cat->id)
            ->assertSee('Dans le type')
            ->assertDontSee('Hors du type');
    }

    public function test_filtre_par_pays(): void
    {
        $pays = Pays::factory()->create(['name' => 'Paysland', 'slug' => 'paysland']);
        Event::factory()->upcoming()->create(['title' => 'Au pays cible', 'pays_id' => $pays->id]);
        Event::factory()->upcoming()->create(['title' => 'Dans un autre pays', 'pays_id' => 9999]);

        Livewire::test(GrilleEvents::class)
            ->set('pays', (string) $pays->id)
            ->assertSee('Au pays cible')
            ->assertDontSee('Dans un autre pays');
    }

    public function test_combinaison_statut_et_type(): void
    {
        $cat = Category::factory()->create(['type' => 'event', 'name' => 'Combo', 'slug' => 'combo-f', 'online' => 1]);
        Event::factory()->upcoming()->create(['title' => 'Cible exacte', 'category_id' => $cat->id]);
        Event::factory()->past()->create(['title' => 'Bon type mauvais statut', 'category_id' => $cat->id]);
        Event::factory()->upcoming()->create(['title' => 'Bon statut mauvais type', 'category_id' => 9999]);

        Livewire::test(GrilleEvents::class)
            ->set('statut', 'upcoming')
            ->set('type', (string) $cat->id)
            ->assertSee('Cible exacte')
            ->assertDontSee('Bon type mauvais statut')
            ->assertDontSee('Bon statut mauvais type');
    }

    public function test_tri_proche_vs_recent(): void
    {
        $vieux = Event::factory()->create(['status' => 'published', 'start_date' => now()->subDays(10), 'end_date' => now()->subDays(9)]);
        $futur = Event::factory()->create(['status' => 'published', 'start_date' => now()->addDays(10), 'end_date' => now()->addDays(11)]);

        $service = app(EventSearchService::class);

        $this->assertSame($vieux->id, $service->recherche(['tri' => 'proche'])->first()->id);
        $this->assertSame($futur->id, $service->recherche(['tri' => 'recent'])->first()->id);
    }

    public function test_les_parametres_de_filtre_sont_lus_depuis_l_url(): void
    {
        Livewire::withQueryParams(['statut' => 'past', 'type' => '7', 'pays' => '3', 'tri' => 'recent'])
            ->test(GrilleEvents::class)
            ->assertSet('statut', 'past')
            ->assertSet('type', '7')
            ->assertSet('pays', '3')
            ->assertSet('tri', 'recent');
    }

    public function test_changer_un_filtre_reinitialise_per_page(): void
    {
        Event::factory()->count(30)->upcoming()->create();

        Livewire::test(GrilleEvents::class)
            ->call('chargerPlus')
            ->assertSet('perPage', 24)
            ->set('statut', 'upcoming')
            ->assertSet('perPage', 12);
    }

    public function test_compte_a_rebours_present_sur_les_a_venir(): void
    {
        Event::factory()->upcoming()->create(['title' => 'Bientot la conf']);

        Livewire::test(GrilleEvents::class)
            ->assertSee('data-countdown', false);
    }

    public function test_pas_de_compte_a_rebours_sur_les_clos(): void
    {
        Event::factory()->past()->create(['title' => 'Conf terminee']);

        Livewire::test(GrilleEvents::class)
            ->assertDontSee('data-countdown', false);
    }
}
