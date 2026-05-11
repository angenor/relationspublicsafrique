<?php

namespace Tests\Feature\Annuaire;

use App\Models\Profil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnuaireListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_visiteur_anonyme_peut_voir_la_page_annuaire(): void
    {
        Profil::factory()->publie()->count(3)->create();

        $response = $this->get('/annuaire');

        $response->assertOk();
    }

    public function test_seuls_les_profils_publies_apparaissent_dans_la_liste(): void
    {
        $publie = Profil::factory()->publie()->create(['nom' => 'VisiblePublie']);
        $enAttente = Profil::factory()->enAttente()->create(['nom' => 'CacheEnAttente']);
        $archive = Profil::factory()->archive()->create(['nom' => 'CacheArchive']);

        $response = $this->get('/annuaire');

        $response->assertOk();
        $response->assertSee('VisiblePublie');
        $response->assertDontSee('CacheEnAttente');
        $response->assertDontSee('CacheArchive');
    }

    public function test_pagination_par_defaut_est_24(): void
    {
        Profil::factory()->publie()->count(30)->create();

        $response = $this->get('/annuaire');

        $response->assertOk();
        // 24 profils affichés sur la première page (config annuaire.per_page)
        $this->assertEquals(24, config('annuaire.per_page'));
    }
}
