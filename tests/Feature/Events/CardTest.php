<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Livewire\Events\GrilleEvents;
use App\Models\Category;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CardTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_card_affiche_les_champs_attendus(): void
    {
        $cat = Category::factory()->create(['type' => 'event', 'name' => 'Conférence', 'slug' => 'conference-x', 'online' => 1]);
        Event::factory()->upcoming()->presentiel()->create([
            'title' => 'Grande conference RP',
            'resume' => 'Un resume concis de la conference.',
            'location' => 'Lomé',
            'category_id' => $cat->id,
        ]);

        Livewire::test(GrilleEvents::class)
            ->assertSee('Grande conference RP')   // titre
            ->assertSee('Un resume concis')        // résumé
            ->assertSee('Lomé')                    // format_label (présentiel → ville)
            ->assertSee('Conférence')              // type
            ->assertSee('À venir');                // badge statut temporel
    }

    public function test_cta_s_inscrire_pour_un_a_venir_interne(): void
    {
        Event::factory()->upcoming()->create([
            'title' => 'Atelier inscriptible',
            'max_participants' => 50,
            'current_participants' => 0,
        ]);

        Livewire::test(GrilleEvents::class)
            ->assertSee("S'inscrire");
    }

    public function test_cta_replay_pour_un_clos_avec_media(): void
    {
        $event = Event::factory()->past()->create(['title' => 'Conference passee']);
        $event->medias()->create([
            'type' => 'replay',
            'url_embed' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'position' => 0,
        ]);

        Livewire::test(GrilleEvents::class)
            ->assertSee('Voir le replay');
    }

    public function test_etat_vide_par_groupe_filtre(): void
    {
        Event::factory()->upcoming()->create(['title' => 'Seul a venir']);

        Livewire::test(GrilleEvents::class)
            ->set('statut', 'past')
            ->assertSee('ne correspond à votre recherche');
    }

    public function test_etat_vide_global_sans_evenement(): void
    {
        Livewire::test(GrilleEvents::class)
            ->assertSee('pour le moment');
    }
}
