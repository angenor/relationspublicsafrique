<?php

declare(strict_types=1);

namespace Tests\Feature\Annuaire;

use App\Models\ConsentementProfil;
use App\Models\Profil;
use App\Models\User;
use App\Notifications\DemandeRetraitConfirmeNotification;
use App\Notifications\ProfilPubliePersonneNotification;
use App\Services\Annuaire\ProfilConsentementService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ConsentementRgpdTest extends TestCase
{
    use RefreshDatabase;

    public function test_consentement_atteste_genere_jeton_et_enregistre_attesteur(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);
        $profil = Profil::factory()->enAttente()->create();

        $service = app(ProfilConsentementService::class);
        $consentement = $service->attester($profil, $admin);

        $this->assertNotNull($consentement->jeton_retrait);
        $this->assertEquals($admin->id, $consentement->atteste_par);
        $this->assertNotNull($consentement->atteste_le);
    }

    public function test_notification_publication_envoyee_a_la_personne_avec_lien_signe(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['type' => 'admin']);
        $profil = Profil::factory()->publie()->create(['email' => 'personne@example.com']);

        $service = app(ProfilConsentementService::class);
        $service->attester($profil, $admin, 'personne@example.com');
        $service->envoyerNotificationPublication($profil->fresh());

        Notification::assertSentOnDemand(ProfilPubliePersonneNotification::class);
    }

    public function test_demande_retrait_via_jeton_archive_le_profil(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);
        $profil = Profil::factory()->publie()->create();

        $service = app(ProfilConsentementService::class);
        $consentement = $service->attester($profil, $admin);
        $token = $consentement->jeton_retrait;

        $traite = $service->traiterDemandeRetrait($token);

        $this->assertNotNull($traite);
        $this->assertEquals('archive', $profil->fresh()->etat_publication);
        $this->assertNotNull($consentement->fresh()->retrait_demande_le);
    }

    public function test_demande_retrait_avec_jeton_expire_retourne_null(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);
        $profil = Profil::factory()->publie()->create();

        $service = app(ProfilConsentementService::class);
        $consentement = $service->attester($profil, $admin);
        $consentement->jeton_expire_le = now()->subDay();
        $consentement->save();

        $traite = $service->traiterDemandeRetrait($consentement->jeton_retrait);

        $this->assertNull($traite);
    }
}
