<?php

declare(strict_types=1);

namespace Tests\Unit\Events;

use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventRelationsTest extends TestCase
{
    use RefreshDatabase;

    public function test_relations_speakers_et_medias(): void
    {
        $event = Event::factory()->create();
        $event->speakers()->create(['nom' => 'Intervenante A', 'position' => 1]);
        $event->speakers()->create(['nom' => 'Intervenant B', 'position' => 0]);
        $event->medias()->create(['type' => 'image', 'chemin' => 'events/medias/a.jpg', 'position' => 0]);

        $this->assertCount(2, $event->speakers);
        $this->assertCount(1, $event->medias);
        // Ordonnés par position.
        $this->assertSame('Intervenant B', $event->speakers->first()->nom);
    }

    public function test_scope_published_exclut_brouillons(): void
    {
        Event::factory()->create(['status' => 'published']);
        Event::factory()->create(['status' => 'draft']);
        Event::factory()->create(['status' => 'cancelled']);

        $this->assertSame(1, Event::published()->count());
    }

    public function test_scopes_temporels(): void
    {
        Event::factory()->upcoming()->create();
        Event::factory()->ongoing()->create();
        Event::factory()->past()->create();

        $this->assertSame(1, Event::upcoming()->count());
        $this->assertSame(1, Event::ongoing()->count());
        $this->assertSame(1, Event::past()->count());
    }

    public function test_suppression_cascade_des_enfants(): void
    {
        $event = Event::factory()->create();
        $event->speakers()->create(['nom' => 'X', 'position' => 0]);
        $event->medias()->create(['type' => 'image', 'chemin' => 'events/medias/x.jpg', 'position' => 0]);

        $event->delete();

        $this->assertSame(0, \App\Models\EventSpeaker::count());
        $this->assertSame(0, \App\Models\EventMedia::count());
    }
}
