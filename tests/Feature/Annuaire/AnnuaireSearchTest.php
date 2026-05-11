<?php

namespace Tests\Feature\Annuaire;

use App\Models\Pays;
use App\Models\Profil;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnuaireSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_recherche_libre_par_nom_insensible_accents_et_casse(): void
    {
        Profil::factory()->publie()->create(['nom' => 'Diop', 'prenom' => 'Marie']);
        Profil::factory()->publie()->create(['nom' => 'Traoré', 'prenom' => 'Aïcha']);
        Profil::factory()->publie()->create(['nom' => 'Smith', 'prenom' => 'John']);

        $response = $this->get('/annuaire?q=traore');

        $response->assertOk();
        $response->assertSee('Traoré');
        $response->assertDontSee('Smith');
    }

    public function test_recherche_libre_sur_pays_via_relation(): void
    {
        $senegal = Pays::factory()->senegal()->create();
        $ci = Pays::factory()->coteDivoire()->create();

        Profil::factory()->publie()->create(['nom' => 'DiopSn', 'pays_id' => $senegal->id]);
        Profil::factory()->publie()->create(['nom' => 'KoneCi', 'pays_id' => $ci->id]);

        $response = $this->get('/annuaire?q=senegal');

        $response->assertOk();
        $response->assertSee('DiopSn');
        $response->assertDontSee('KoneCi');
    }

    public function test_recherche_libre_sur_organisation_normalisee(): void
    {
        Profil::factory()->publie()->create([
            'nom' => 'Kone',
            'organisation' => 'Cabinet Côté Influence',
        ]);
        Profil::factory()->publie()->create([
            'nom' => 'Sarr',
            'organisation' => 'Agence Tropic',
        ]);

        $response = $this->get('/annuaire?q=cote influence');

        $response->assertOk();
        $response->assertSee('Kone');
        $response->assertDontSee('Sarr');
    }
}
