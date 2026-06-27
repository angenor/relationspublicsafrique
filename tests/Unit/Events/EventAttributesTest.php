<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventAttributesTest extends TestCase
{
    use RefreshDatabase;

    public function test_temporal_status_derive_des_dates(): void
    {
        $upcoming = Event::factory()->upcoming()->create();
        $ongoing = Event::factory()->ongoing()->create();
        $past = Event::factory()->past()->create();

        $this->assertSame('upcoming', $upcoming->temporal_status);
        $this->assertSame('ongoing', $ongoing->temporal_status);
        $this->assertSame('past', $past->temporal_status);

        $this->assertSame('À venir', $upcoming->temporal_status_label);
        $this->assertSame('En cours', $ongoing->temporal_status_label);
        $this->assertSame('Clos', $past->temporal_status_label);
    }

    public function test_can_register_ne_depend_plus_de_online(): void
    {
        // Présentiel (online=false) publié, à venir, interne → inscriptible (R1).
        $presentiel = Event::factory()->upcoming()->presentiel()->create([
            'max_participants' => 100,
            'current_participants' => 0,
        ]);

        $this->assertTrue($presentiel->can_register);
    }

    public function test_can_register_faux_si_brouillon_clos_externe_complet_ou_deadline(): void
    {
        $this->assertFalse(Event::factory()->upcoming()->draft()->create()->can_register);
        $this->assertFalse(Event::factory()->past()->create()->can_register);
        $this->assertFalse(Event::factory()->upcoming()->external()->create()->can_register);

        $complet = Event::factory()->upcoming()->create([
            'max_participants' => 10,
            'current_participants' => 10,
        ]);
        $this->assertFalse($complet->can_register);

        $deadlinePassee = Event::factory()->upcoming()->create([
            'registration_deadline' => now()->subDay(),
        ]);
        $this->assertFalse($deadlinePassee->can_register);
    }

    public function test_format_label(): void
    {
        $enLigne = Event::factory()->online()->create();
        $presentiel = Event::factory()->presentiel()->create(['location' => 'Lomé']);

        $this->assertSame('En ligne', $enLigne->format_label);
        $this->assertSame('Lomé', $presentiel->format_label);
    }

    public function test_has_replay_et_has_post_event_media(): void
    {
        $past = Event::factory()->past()->create(['compte_rendu' => null]);
        $this->assertFalse($past->has_replay);
        $this->assertFalse($past->has_post_event_media);

        $past->medias()->create(['type' => 'replay', 'url_embed' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'position' => 0]);
        $this->assertTrue($past->fresh()->has_replay);
        $this->assertTrue($past->fresh()->has_post_event_media);

        // Un compte rendu seul suffit pour has_post_event_media.
        $clos = Event::factory()->past()->create(['compte_rendu' => '<p>Bilan</p>']);
        $this->assertTrue($clos->has_post_event_media);
        $this->assertFalse($clos->has_replay);

        // Un événement à venir n'est jamais « post-événement », même avec un média.
        $aVenir = Event::factory()->upcoming()->create();
        $aVenir->medias()->create(['type' => 'image', 'chemin' => 'events/medias/x.jpg', 'position' => 0]);
        $this->assertFalse($aVenir->fresh()->has_post_event_media);
    }

    public function test_cta_selon_etat(): void
    {
        $this->assertSame('view_details', Event::factory()->upcoming()->cancelled()->create()->cta);

        $replay = Event::factory()->past()->create();
        $replay->medias()->create(['type' => 'replay', 'url_embed' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'position' => 0]);
        $this->assertSame('view_replay', $replay->fresh()->cta);

        $this->assertSame('view_details', Event::factory()->past()->create(['compte_rendu' => null])->cta);
        $this->assertSame('register_external', Event::factory()->upcoming()->external()->create()->cta);

        $interne = Event::factory()->upcoming()->create(['max_participants' => 50, 'current_participants' => 0]);
        $this->assertSame('register_internal', $interne->cta);

        $complet = Event::factory()->upcoming()->create(['max_participants' => 10, 'current_participants' => 10]);
        $this->assertSame('view_details', $complet->cta);
    }
}
