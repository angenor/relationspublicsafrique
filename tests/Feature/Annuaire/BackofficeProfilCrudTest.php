<?php

declare(strict_types=1);

namespace Tests\Feature\Annuaire;

use App\Models\Profil;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BackofficeProfilCrudTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['type' => 'admin']);
    }

    private function editeur(): User
    {
        return User::factory()->create(['type' => 'editeur']);
    }

    public function test_admin_voit_tous_les_profils(): void
    {
        $admin = $this->admin();
        $profilA = Profil::factory()->create();
        $profilB = Profil::factory()->create();

        $this->assertTrue($admin->can('viewAny', Profil::class));
        $this->assertTrue($admin->can('view', $profilA));
        $this->assertTrue($admin->can('view', $profilB));
    }

    public function test_editeur_ne_voit_que_ses_propres_profils(): void
    {
        $editeur = $this->editeur();
        $autre = $this->editeur();

        $sien = Profil::factory()->create(['user_id' => $editeur->id]);
        $pasSien = Profil::factory()->create(['user_id' => $autre->id]);

        $this->assertTrue($editeur->can('view', $sien));
        $this->assertFalse($editeur->can('view', $pasSien));
        $this->assertFalse($editeur->can('delete', $sien));
        $this->assertTrue($editeur->can('update', $sien));
        $this->assertFalse($editeur->can('update', $pasSien));
    }

    public function test_visiteur_anonyme_ne_peut_acceder_au_backoffice(): void
    {
        $response = $this->get('/admin/profils');
        // Filament redirige vers le login pour les visiteurs anonymes.
        $this->assertContains($response->status(), [302, 403, 401]);
    }

    public function test_editeur_ne_peut_pas_approuver_un_profil(): void
    {
        $editeur = $this->editeur();
        $profil = Profil::factory()->enAttente()->create(['user_id' => $editeur->id]);

        $this->assertFalse($editeur->can('approve', $profil));
        $this->assertFalse($editeur->can('reject', $profil));
        $this->assertFalse($editeur->can('archive', $profil));
    }
}
