<?php

declare(strict_types=1);

namespace Tests\Feature\Projets;

use App\Filament\Resources\ProjetResource\Pages\CreateProjet;
use App\Filament\Resources\ProjetResource\Pages\EditProjet;
use App\Models\Pays;
use App\Models\Projet;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AdminProjetResourceTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Filament::setCurrentPanel(Filament::getPanel('admin'));
    }

    private function admin(): User
    {
        return User::factory()->create(['type' => 'admin']);
    }

    private function editeur(): User
    {
        return User::factory()->create(['type' => 'editeur']);
    }

    public function test_creation_d_un_projet_complet_publie_le_rend_visible(): void
    {
        $this->actingAs($this->admin());
        $pays = Pays::factory()->create(['name' => 'Testland', 'slug' => 'testland']);

        Livewire::test(CreateProjet::class)
            ->fillForm([
                'titre' => 'Projet back-office complet',
                'slug' => 'projet-back-office-complet',
                'resume' => 'Un résumé concis du projet.',
                'statut' => 'actif',
                'portee' => 'pays',
                'pays_id' => $pays->id,
                'is_published' => true,
                'published_at' => now()->subDay(),
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $this->assertDatabaseHas('projets', [
            'slug' => 'projet-back-office-complet',
            'statut' => 'actif',
            'is_published' => 1,
        ]);

        // Visible publiquement (SC-003, US3-1).
        $this->assertSame(1, Projet::published()->where('slug', 'projet-back-office-complet')->count());
        $this->get('/projets/projet-back-office-complet')->assertOk()->assertSee('Projet back-office complet', false);
    }

    public function test_la_modification_du_statut_est_refletee_cote_public(): void
    {
        $this->actingAs($this->admin());
        $pays = Pays::factory()->create(['name' => 'Editland', 'slug' => 'editland']);
        $projet = Projet::factory()->statut('actif')->withPays((int) $pays->id)->create(['slug' => 'projet-a-modifier']);

        Livewire::test(EditProjet::class, ['record' => $projet->getKey()])
            ->fillForm(['statut' => 'realise'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('realise', $projet->fresh()->statut);
        $this->get('/projets/projet-a-modifier')->assertOk()->assertSee('Réalisé', false);
    }

    public function test_la_suppression_retire_le_projet_et_renvoie_404(): void
    {
        $projet = Projet::factory()->create(['slug' => 'projet-a-supprimer']);

        $this->get('/projets/projet-a-supprimer')->assertOk();

        $projet->delete(); // soft delete

        $this->assertSame(0, Projet::published()->where('slug', 'projet-a-supprimer')->count());
        $this->get('/projets/projet-a-supprimer')->assertNotFound();
    }

    public function test_un_projet_non_publie_est_absent_du_public(): void
    {
        Projet::factory()->draft()->create(['slug' => 'brouillon-admin']);

        $this->assertSame(0, Projet::published()->where('slug', 'brouillon-admin')->count());
        $this->get('/projets/brouillon-admin')->assertNotFound();
    }

    public function test_les_medias_associes_sont_rendus_sur_le_detail(): void
    {
        $projet = Projet::factory()->create(['slug' => 'projet-avec-medias']);
        $projet->medias()->create(['type' => 'image', 'chemin' => 'projets/galerie/photo-admin.jpg', 'legende' => 'Photo admin', 'position' => 0]);
        $projet->medias()->create(['type' => 'video', 'url_embed' => 'https://www.youtube.com/watch?v=dQw4w9WgXcQ', 'position' => 1]);

        $this->get('/projets/projet-avec-medias')
            ->assertOk()
            ->assertSee('projets/galerie/photo-admin.jpg', false)
            ->assertSee('youtube-nocookie.com/embed/dQw4w9WgXcQ', false);
    }

    public function test_le_backoffice_est_protege_pour_un_anonyme(): void
    {
        // FR-024 : la liste et le formulaire ne sont pas accessibles sans authentification.
        $this->assertContains($this->get('/admin/projets')->status(), [302, 403, 401]);
        $this->assertContains($this->get('/admin/projets/create')->status(), [302, 403, 401]);
    }

    public function test_un_utilisateur_lambda_ne_peut_pas_acceder_au_backoffice(): void
    {
        // FR-024 : un utilisateur sans rôle staff est refusé au niveau du panneau.
        $this->actingAs(User::factory()->create(['type' => 'user']));
        $this->assertContains($this->get('/admin/projets')->status(), [302, 403, 401]);
    }

    public function test_un_editeur_ne_peut_pas_acceder_aux_projets(): void
    {
        // FR-024 : l'éditeur accède au panneau (annuaire) mais PAS aux projets (policy admin-only).
        $this->actingAs($this->editeur());
        $this->assertContains($this->get('/admin/projets')->status(), [403, 302]);
        $this->assertContains($this->get('/admin/projets/create')->status(), [403, 302]);
        $this->assertFalse($this->editeur()->can('viewAny', \App\Models\Projet::class));
        $this->assertTrue($this->admin()->can('viewAny', \App\Models\Projet::class));
    }
}
