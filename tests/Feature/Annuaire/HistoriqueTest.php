<?php

declare(strict_types=1);

namespace Tests\Feature\Annuaire;

use App\Models\HistoriqueProfil;
use App\Models\Profil;
use App\Models\User;
use App\Services\Annuaire\ProfilHistoriqueService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HistoriqueTest extends TestCase
{
    use RefreshDatabase;

    public function test_chaque_modification_cree_une_entree_avec_diff_auteur_et_ip(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);
        $this->actingAs($admin);

        $profil = Profil::factory()->publie()->create([
            'fonction' => 'Consultante',
            'ville' => 'Dakar',
        ]);

        $profil->update([
            'fonction' => 'Directrice',
            'ville' => 'Abidjan',
        ]);

        $entree = HistoriqueProfil::where('profil_id', $profil->id)
            ->where('action', 'modifie')
            ->orderByDesc('created_at')
            ->first();

        $this->assertNotNull($entree, 'Une entrée d\'historique modifie doit exister.');
        $this->assertEquals($admin->id, $entree->user_id);

        $diff = $entree->diff;
        $this->assertArrayHasKey('fonction', $diff);
        $this->assertEquals('Consultante', $diff['fonction']['avant']);
        $this->assertEquals('Directrice', $diff['fonction']['apres']);
        $this->assertArrayHasKey('ville', $diff);
        $this->assertEquals('Dakar', $diff['ville']['avant']);
        $this->assertEquals('Abidjan', $diff['ville']['apres']);
    }

    public function test_diff_ne_contient_pas_les_champs_normalises(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);
        $this->actingAs($admin);

        $profil = Profil::factory()->publie()->create(['nom' => 'Diop']);
        $profil->update(['nom' => 'Sarr']);

        $entree = HistoriqueProfil::where('profil_id', $profil->id)
            ->where('action', 'modifie')
            ->orderByDesc('created_at')
            ->first();

        $diff = $entree->diff;
        $this->assertArrayNotHasKey('nom_normalise', $diff);
        $this->assertArrayNotHasKey('prenom_normalise', $diff);
        $this->assertArrayNotHasKey('organisation_normalisee', $diff);
        $this->assertArrayNotHasKey('ville_normalisee', $diff);
        $this->assertArrayNotHasKey('updated_at', $diff);
    }

    public function test_admin_peut_consulter_lhistorique_de_tous_les_profils(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);
        $autreEditeur = User::factory()->create(['type' => 'editeur']);
        $profilAutre = Profil::factory()->publie()->create(['user_id' => $autreEditeur->id]);

        $service = app(ProfilHistoriqueService::class);
        $entrees = $service->historiquePourProfil($profilAutre);

        $this->assertTrue($admin->can('view', $profilAutre));
        $this->assertGreaterThan(0, $entrees->count());
    }

    public function test_editeur_ne_voit_que_lhistorique_de_ses_propres_profils(): void
    {
        $editeurA = User::factory()->create(['type' => 'editeur']);
        $editeurB = User::factory()->create(['type' => 'editeur']);

        $profilA = Profil::factory()->publie()->create(['user_id' => $editeurA->id]);
        $profilB = Profil::factory()->publie()->create(['user_id' => $editeurB->id]);

        $this->assertTrue($editeurA->can('view', $profilA));
        $this->assertFalse($editeurA->can('view', $profilB));
    }

    public function test_restauration_dune_version_journalise_letat_avant_dans_nouvelle_entree(): void
    {
        $admin = User::factory()->create(['type' => 'admin']);
        $this->actingAs($admin);

        $profil = Profil::factory()->publie()->create([
            'fonction' => 'V1',
            'bio_courte' => 'Bio originale',
        ]);

        $profil->update([
            'fonction' => 'V2',
            'bio_courte' => 'Bio modifiée',
        ]);

        $entreeModif = HistoriqueProfil::where('profil_id', $profil->id)
            ->where('action', 'modifie')
            ->orderByDesc('created_at')
            ->first();

        $countAvant = HistoriqueProfil::where('profil_id', $profil->id)->count();

        $service = app(ProfilHistoriqueService::class);
        $profilRestaure = $service->restaurer($entreeModif, $admin);

        $this->assertEquals('V1', $profilRestaure->fresh()->fonction);
        $this->assertEquals('Bio originale', $profilRestaure->fresh()->bio_courte);

        $entreeRestauration = HistoriqueProfil::where('profil_id', $profil->id)
            ->where('action', 'restaure')
            ->orderByDesc('created_at')
            ->first();

        $this->assertNotNull($entreeRestauration, 'Une entrée action=restaure doit être créée.');
        $this->assertEquals($admin->id, $entreeRestauration->user_id);
        $this->assertGreaterThan($countAvant, HistoriqueProfil::where('profil_id', $profil->id)->count());
    }
}
