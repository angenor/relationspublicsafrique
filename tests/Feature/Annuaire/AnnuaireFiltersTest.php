<?php

namespace Tests\Feature\Annuaire;

use App\Models\DomaineExpertise;
use App\Models\Pays;
use App\Models\Profil;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnuaireFiltersTest extends TestCase
{
    use RefreshDatabase;

    public function test_filtre_pays_par_slug(): void
    {
        $sn = Pays::factory()->senegal()->create();
        $ci = Pays::factory()->coteDivoire()->create();

        Profil::factory()->publie()->create(['nom' => 'DiopSn', 'pays_id' => $sn->id]);
        Profil::factory()->publie()->create(['nom' => 'KoneCi', 'pays_id' => $ci->id]);

        $response = $this->get('/annuaire?pays=senegal');

        $response->assertOk();
        $response->assertSee('DiopSn');
        $response->assertDontSee('KoneCi');
    }

    public function test_filtre_type_profil(): void
    {
        Profil::factory()->publie()->create(['nom' => 'Expert1', 'type_profil' => 'expert']);
        Profil::factory()->publie()->create(['nom' => 'Etudiant1', 'type_profil' => 'etudiant']);

        $response = $this->get('/annuaire?type=expert');

        $response->assertOk();
        $response->assertSee('Expert1');
        $response->assertDontSee('Etudiant1');
    }

    public function test_filtre_domaine_expertise(): void
    {
        $comm = DomaineExpertise::create(['libelle' => 'Communication', 'slug' => 'communication']);
        $lob = DomaineExpertise::create(['libelle' => 'Lobbying', 'slug' => 'lobbying']);

        $p1 = Profil::factory()->publie()->create(['nom' => 'CommExpert']);
        $p1->domainesExpertise()->attach($comm->id);

        $p2 = Profil::factory()->publie()->create(['nom' => 'LobExpert']);
        $p2->domainesExpertise()->attach($lob->id);

        $response = $this->get('/annuaire?'.http_build_query(['domaine' => ['communication']]));

        $response->assertOk();
        $response->assertSee('CommExpert');
        $response->assertDontSee('LobExpert');
    }

    public function test_filtre_tag(): void
    {
        $afro = Tag::create(['libelle' => 'Afrique de l\'Ouest', 'slug' => 'afrique-ouest']);

        $p1 = Profil::factory()->publie()->create(['nom' => 'WestAfrica']);
        $p1->tags()->attach($afro->id);

        Profil::factory()->publie()->create(['nom' => 'NoTag']);

        $response = $this->get('/annuaire?'.http_build_query(['tag' => ['afrique-ouest']]));

        $response->assertOk();
        $response->assertSee('WestAfrica');
        $response->assertDontSee('NoTag');
    }

    public function test_filtres_combines_ET_logique(): void
    {
        $sn = Pays::factory()->senegal()->create();
        $comm = DomaineExpertise::create(['libelle' => 'Comm', 'slug' => 'comm']);

        $match = Profil::factory()->publie()->create([
            'nom' => 'MatchAll',
            'type_profil' => 'expert',
            'pays_id' => $sn->id,
        ]);
        $match->domainesExpertise()->attach($comm->id);

        $partial = Profil::factory()->publie()->create([
            'nom' => 'PartialMatch',
            'type_profil' => 'expert',
            'pays_id' => $sn->id,
        ]);

        $response = $this->get('/annuaire?'.http_build_query([
            'pays' => 'senegal',
            'type' => 'expert',
            'domaine' => ['comm'],
        ]));

        $response->assertOk();
        $response->assertSee('MatchAll');
        $response->assertDontSee('PartialMatch');
    }

    public function test_aucun_filtre_retourne_tout(): void
    {
        Profil::factory()->publie()->count(5)->create();

        $response = $this->get('/annuaire');

        $response->assertOk();
    }
}
