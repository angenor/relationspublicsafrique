<?php

declare(strict_types=1);

namespace Tests\Feature\Events;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Inscription interne (C1/U1) — route events.register branchée sur
 * EventRegistrationController@register. L'inscription est réservée aux connectés.
 */
class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_utilisateur_connecte_eligible_s_inscrit(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->upcoming()->create(['max_participants' => 50, 'current_participants' => 0]);

        $this->actingAs($user)
            ->postJson(route('events.register', $event))
            ->assertOk()
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('event_registrations', [
            'event_id' => $event->id,
            'user_id' => $user->id,
            'status' => 'registered',
        ]);
        $this->assertSame(1, $event->fresh()->current_participants);
    }

    public function test_un_visiteur_anonyme_est_refuse_et_redirige(): void
    {
        $event = Event::factory()->upcoming()->create();

        $this->postJson(route('events.register', $event))
            ->assertStatus(401)
            ->assertJson(['success' => false])
            ->assertJsonPath('redirect', route('login'));
    }

    public function test_le_doublon_est_refuse(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->upcoming()->create(['max_participants' => 50, 'current_participants' => 0]);

        $this->actingAs($user)->postJson(route('events.register', $event))->assertOk();
        $this->actingAs($user)->postJson(route('events.register', $event))->assertStatus(400);

        // Pas de double comptage des participants.
        $this->assertSame(1, $event->fresh()->current_participants);
    }

    public function test_la_capacite_atteinte_refuse_l_inscription(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->upcoming()->create(['max_participants' => 1, 'current_participants' => 1]);

        $this->actingAs($user)
            ->postJson(route('events.register', $event))
            ->assertStatus(400);
    }

    public function test_la_deadline_depassee_refuse_l_inscription(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->upcoming()->create(['registration_deadline' => now()->subDay()]);

        $this->actingAs($user)
            ->postJson(route('events.register', $event))
            ->assertStatus(400);
    }

    public function test_un_evenement_a_inscription_externe_refuse_le_parcours_interne(): void
    {
        $user = User::factory()->create();
        $event = Event::factory()->upcoming()->external()->create();

        $this->actingAs($user)
            ->postJson(route('events.register', $event))
            ->assertStatus(400);
    }
}
