<?php

declare(strict_types=1);

namespace Tests\Feature\Annuaire;

use App\Models\ConsentementProfil;
use App\Models\Profil;
use App\Models\User;
use App\Notifications\ProfilPubliePersonneNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class RappelConsentementCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_commande_envoie_un_rappel_aux_profils_proches_expiration(): void
    {
        Notification::fake();

        $admin = User::factory()->create(['type' => 'admin']);

        $bientotExpire = Profil::factory()->publie()->create(['email' => 'bientot@example.com']);
        $consentement = ConsentementProfil::create([
            'profil_id' => $bientotExpire->id,
            'atteste_par' => $admin->id,
            'atteste_le' => now()->subDays(85),
            'email_notification_envoye_a' => 'bientot@example.com',
            'jeton_retrait' => str_repeat('a', 64),
            'jeton_expire_le' => now()->addDays(3),
        ]);

        $loin = Profil::factory()->publie()->create(['email' => 'loin@example.com']);
        ConsentementProfil::create([
            'profil_id' => $loin->id,
            'atteste_par' => $admin->id,
            'atteste_le' => now()->subDays(10),
            'email_notification_envoye_a' => 'loin@example.com',
            'jeton_retrait' => str_repeat('b', 64),
            'jeton_expire_le' => now()->addDays(60),
        ]);

        $this->artisan('annuaire:envoyer-rappels-consentement')->assertExitCode(0);

        Notification::assertSentOnDemand(ProfilPubliePersonneNotification::class, function ($notif, $channels, $notifiable) {
            $route = $notifiable->routes['mail'] ?? null;
            $emails = is_array($route) ? $route : (is_string($route) ? [$route] : []);

            return in_array('bientot@example.com', $emails, true);
        });
        Notification::assertSentOnDemandTimes(ProfilPubliePersonneNotification::class, 1);

        $this->assertNotNull($consentement->fresh()->email_notification_envoye_le);
    }
}
