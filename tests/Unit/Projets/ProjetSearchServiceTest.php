<?php

declare(strict_types=1);

namespace Tests\Unit\Projets;

use App\Models\Category;
use App\Models\Pays;
use App\Models\Projet;
use App\Services\Projets\ProjetSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjetSearchServiceTest extends TestCase
{
    use RefreshDatabase;

    private function service(): ProjetSearchService
    {
        return new ProjetSearchService;
    }

    public function test_recherche_par_titre_insensible_aux_accents(): void
    {
        $match = Projet::factory()->create(['titre' => 'Élection présidentielle']);
        $autre = Projet::factory()->create(['titre' => 'Sujet économique divers']);

        $ids = $this->service()->recherche(['q' => 'election'])->pluck('id');

        $this->assertTrue($ids->contains($match->id));
        $this->assertFalse($ids->contains($autre->id));
    }

    public function test_filtre_par_thematique(): void
    {
        $cat = Category::factory()->create(['type' => 'projet', 'slug' => 'plaidoyer', 'name' => 'Plaidoyer']);
        $dans = Projet::factory()->create();
        $dans->categories()->attach($cat->id, ['position' => 0]);
        $hors = Projet::factory()->create();

        $ids = $this->service()->recherche(['thematique' => $cat->id])->pluck('id');

        $this->assertTrue($ids->contains($dans->id));
        $this->assertFalse($ids->contains($hors->id));
    }

    public function test_filtre_par_zone_pays(): void
    {
        $pays = Pays::factory()->create(['name' => 'Testland', 'slug' => 'testland']);
        $dans = Projet::factory()->withPays((int) $pays->id)->create();
        $hors = Projet::factory()->regional()->create();

        $ids = $this->service()->recherche(['zone' => 'pays:'.$pays->id])->pluck('id');

        $this->assertTrue($ids->contains($dans->id));
        $this->assertFalse($ids->contains($hors->id));
    }

    public function test_filtre_par_zone_portee(): void
    {
        $regional = Projet::factory()->regional()->create();
        $national = Projet::factory()->withPays(7)->create();

        $ids = $this->service()->recherche(['zone' => 'portee:regional'])->pluck('id');

        $this->assertTrue($ids->contains($regional->id));
        $this->assertFalse($ids->contains($national->id));
    }

    public function test_filtre_par_statut(): void
    {
        $actif = Projet::factory()->statut('actif')->create();
        $realise = Projet::factory()->statut('realise')->create();

        $ids = $this->service()->recherche(['statut' => 'actif'])->pluck('id');

        $this->assertTrue($ids->contains($actif->id));
        $this->assertFalse($ids->contains($realise->id));
    }

    public function test_tri_alpha(): void
    {
        Projet::factory()->create(['titre' => 'Zèbre']);
        Projet::factory()->create(['titre' => 'Abeille']);

        $titres = $this->service()->recherche(['tri' => 'alpha'])->pluck('titre')->all();

        $this->assertSame('Abeille', $titres[0]);
    }

    public function test_combinaison_de_filtres_est_un_et_logique(): void
    {
        $cat = Category::factory()->create(['type' => 'projet', 'slug' => 'comm', 'name' => 'Comm']);

        $bon = Projet::factory()->statut('actif')->create();
        $bon->categories()->attach($cat->id, ['position' => 0]);

        $mauvaisStatut = Projet::factory()->statut('realise')->create();
        $mauvaisStatut->categories()->attach($cat->id, ['position' => 0]);

        $mauvaiseCat = Projet::factory()->statut('actif')->create();

        $ids = $this->service()->recherche(['statut' => 'actif', 'thematique' => $cat->id])->pluck('id');

        $this->assertTrue($ids->contains($bon->id));
        $this->assertFalse($ids->contains($mauvaisStatut->id));
        $this->assertFalse($ids->contains($mauvaiseCat->id));
    }

    public function test_seuls_les_projets_publies_sont_retournes(): void
    {
        Projet::factory()->create(['is_published' => true, 'published_at' => now()->subDay()]);
        Projet::factory()->draft()->create();
        Projet::factory()->scheduled()->create();

        $this->assertSame(1, $this->service()->recherche([])->count());
    }
}
