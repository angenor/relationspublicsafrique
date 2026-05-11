<?php

declare(strict_types=1);

namespace Tests\Feature\Annuaire;

use App\Models\DemandeModeration;
use App\Models\HistoriqueProfil;
use App\Models\Profil;
use App\Models\User;
use App\Notifications\ProfilModerationDecisionNotification;
use App\Services\Annuaire\ProfilModerationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ModerationWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_profil_cree_par_editeur_passe_en_attente_par_defaut(): void
    {
        $editeur = User::factory()->create(['type' => 'editeur']);
        $profil = Profil::factory()->enAttente()->create(['user_id' => $editeur->id]);

        $this->assertEquals('en_attente', $profil->etat_publication);
    }

    public function test_approbation_par_admin_passe_le_profil_en_publie(): void
    {
        Notification::fake();

        $editeur = User::factory()->create(['type' => 'editeur']);
        $admin = User::factory()->create(['type' => 'admin']);
        $profil = Profil::factory()->enAttente()->create(['user_id' => $editeur->id]);

        app(ProfilModerationService::class)->approve($profil, $admin);

        $this->assertEquals('publie', $profil->fresh()->etat_publication);
        $this->assertDatabaseHas('demandes_moderation', [
            'profil_id' => $profil->id,
            'moderateur_id' => $admin->id,
            'decision' => 'approuve',
        ]);
        $this->assertTrue(HistoriqueProfil::where('profil_id', $profil->id)->where('action', 'approuve')->exists());
        Notification::assertSentTo($editeur, ProfilModerationDecisionNotification::class);
    }

    public function test_rejet_avec_motif_par_admin_notifie_lediteur(): void
    {
        Notification::fake();

        $editeur = User::factory()->create(['type' => 'editeur']);
        $admin = User::factory()->create(['type' => 'admin']);
        $profil = Profil::factory()->enAttente()->create(['user_id' => $editeur->id]);

        app(ProfilModerationService::class)->reject($profil, $admin, 'Coordonnées manquantes');

        $this->assertEquals('en_attente', $profil->fresh()->etat_publication);
        $this->assertDatabaseHas('demandes_moderation', [
            'profil_id' => $profil->id,
            'decision' => 'rejete',
            'motif' => 'Coordonnées manquantes',
        ]);
        Notification::assertSentTo($editeur, ProfilModerationDecisionNotification::class);
    }

    public function test_editeur_ne_peut_pas_approuver_son_propre_profil(): void
    {
        $editeur = User::factory()->create(['type' => 'editeur']);
        $profil = Profil::factory()->enAttente()->create(['user_id' => $editeur->id]);

        $this->assertFalse($editeur->can('approve', $profil));
    }
}
