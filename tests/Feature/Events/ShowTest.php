<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ShowTest extends TestCase
{
    use RefreshDatabase;

    private function url(Event $event): string
    {
        return route('events.show', ['slug' => $event->slug, 'id' => $event->id]);
    }

    public function test_le_detail_publie_affiche_les_champs(): void
    {
        $event = Event::factory()->upcoming()->create([
            'title' => 'Conference detaillee',
            'slug' => 'conference-detaillee',
            'description' => '<p>Description complete du contenu</p>',
            'objectifs' => 'Objectif principal du forum',
            'programme' => '<p>Au programme detaille</p>',
            'public_cible' => 'Les professionnels du secteur',
        ]);
        $event->speakers()->create(['nom' => 'Intervenante Test', 'role' => 'Experte', 'position' => 0]);

        $this->get($this->url($event))
            ->assertOk()
            ->assertSee('Conference detaillee')
            ->assertSee('Description complete du contenu', false)
            ->assertSee('Objectif principal du forum')
            ->assertSee('Au programme detaille', false)
            ->assertSee('Intervenante Test')
            ->assertSee('Les professionnels du secteur');
    }

    public function test_les_sections_vides_sont_masquees(): void
    {
        $event = Event::factory()->upcoming()->create([
            'slug' => 'evenement-minimal',
            'objectifs' => null,
            'programme' => null,
            'public_cible' => null,
            'compte_rendu' => null,
        ]);

        $this->get($this->url($event))
            ->assertOk()
            ->assertDontSee('>Objectifs<', false)
            ->assertDontSee('>Programme<', false)
            ->assertDontSee('>Intervenants<', false)
            ->assertDontSee('>Public cible<', false)
            ->assertDontSee('>Compte rendu<', false);
    }

    public function test_un_brouillon_donne_404(): void
    {
        $event = Event::factory()->upcoming()->draft()->create(['slug' => 'brouillon-detail']);

        $this->get($this->url($event))->assertNotFound();
    }

    public function test_un_evenement_annule_donne_404(): void
    {
        // Un événement annulé n'est pas publié → invisible (FR-006).
        $event = Event::factory()->upcoming()->cancelled()->create(['slug' => 'evenement-annule']);

        $this->get($this->url($event))->assertNotFound();
    }

    public function test_un_slug_id_inconnu_donne_404(): void
    {
        $this->get('/evenements/inexistant-99999')->assertNotFound();
    }

    public function test_cta_inscription_interne_pour_visiteur_anonyme(): void
    {
        $event = Event::factory()->upcoming()->create([
            'slug' => 'cta-interne',
            'max_participants' => 50,
            'current_participants' => 0,
        ]);

        // Visiteur anonyme → invitation à se connecter (inscription réservée aux connectés).
        $this->get($this->url($event))
            ->assertOk()
            ->assertSee('Se connecter pour s', false);
    }

    public function test_cta_externe_pointe_vers_le_lien(): void
    {
        $event = Event::factory()->upcoming()->external('https://exemple.org/billet')->create(['slug' => 'cta-externe']);

        $this->get($this->url($event))
            ->assertOk()
            ->assertSee('https://exemple.org/billet', false);
    }

    public function test_cta_replay_pour_un_clos_avec_media(): void
    {
        $event = Event::factory()->past()->create(['slug' => 'cta-replay']);
        $event->medias()->create([
            'type' => 'replay',
            'url_embed' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ',
            'position' => 0,
        ]);

        $this->get($this->url($event))
            ->assertOk()
            ->assertSee('Voir replay / photos / documents')
            ->assertSee('youtube-nocookie.com/embed/dQw4w9WgXcQ', false);
    }
}
