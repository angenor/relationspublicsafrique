<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Models\Category;
use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Garde-fous des dettes résolues : gate `online`→`published` (R1/F1) sur toutes
 * les routes publiques, route d'inscription `events.register` (C1), et conservation
 * de l'entrée « Lomé COM' TOUR » (lien vers events.index).
 */
class NonRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_toutes_les_routes_publiques_repondent_200_et_affichent_les_presentiels(): void
    {
        $cat = Category::factory()->create(['type' => 'event', 'name' => 'Conference', 'slug' => 'conference-nr', 'online' => 1]);
        $event = Event::factory()->upcoming()->presentiel()->create([
            'title' => 'Presentiel publie NR',
            'slug' => 'presentiel-publie-nr',
            'category_id' => $cat->id,
        ]);

        // R1/F1 : un événement présentiel publié doit apparaître sur CHAQUE route.
        $this->get(route('events.index'))->assertOk()->assertSee('Presentiel publie NR');
        $this->get(route('events.show', ['slug' => $event->slug, 'id' => $event->id]))->assertOk()->assertSee('Presentiel publie NR');
        $this->get(route('events.category', ['slug' => $cat->slug]))->assertOk()->assertSee('Presentiel publie NR');
        $this->get(route('events.calendar'))->assertOk()->assertSee('Presentiel publie NR');
        $this->get(route('events.search', ['q' => 'Presentiel']))->assertOk()->assertSee('Presentiel publie NR');
    }

    public function test_la_route_inscription_interne_est_resolue_et_fonctionnelle(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->upcoming()->create(['max_participants' => 10, 'current_participants' => 0]);

        // La route existe et est nommée (C1).
        $this->assertIsString(route('events.register', $event));

        $this->actingAs($user)
            ->postJson(route('events.register', $event))
            ->assertOk()
            ->assertJson(['success' => true]);
    }

    public function test_l_entree_lome_comtour_reste_intacte(): void
    {
        // events.index (cible du lien « Lomé COM' TOUR ») répond 200 et conserve
        // le CTA d'inscription COM' TOUR (route lome.tour.register).
        Event::factory()->upcoming()->create();

        $this->get(route('events.index'))
            ->assertOk()
            ->assertSee(route('lome.tour.register'), false);
    }
}
