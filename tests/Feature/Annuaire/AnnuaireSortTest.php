<?php

namespace Tests\Feature\Annuaire;

use App\Models\Profil;
use App\Services\Annuaire\ProfilSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnnuaireSortTest extends TestCase
{
    use RefreshDatabase;

    public function test_tri_alpha_par_nom_asc(): void
    {
        Profil::factory()->publie()->create(['nom' => 'Zola']);
        Profil::factory()->publie()->create(['nom' => 'Adams']);
        Profil::factory()->publie()->create(['nom' => 'Martin']);

        $service = app(ProfilSearchService::class);
        $resultats = $service->recherche(['tri' => 'alpha'])->get();

        $this->assertEquals(['Adams', 'Martin', 'Zola'], $resultats->pluck('nom')->all());
    }

    public function test_tri_recent_par_created_at_desc(): void
    {
        $vieux = Profil::factory()->publie()->create(['nom' => 'Vieux', 'created_at' => now()->subDays(10)]);
        $recent = Profil::factory()->publie()->create(['nom' => 'Recent', 'created_at' => now()->subDay()]);
        $milieu = Profil::factory()->publie()->create(['nom' => 'Milieu', 'created_at' => now()->subDays(5)]);

        $service = app(ProfilSearchService::class);
        $resultats = $service->recherche(['tri' => 'recent'])->get();

        $this->assertEquals(['Recent', 'Milieu', 'Vieux'], $resultats->pluck('nom')->all());
    }

    public function test_tri_pertinence_quand_recherche_libre(): void
    {
        Profil::factory()->publie()->create(['nom' => 'Diop', 'prenom' => 'Marie']);
        Profil::factory()->publie()->create(['nom' => 'Autre', 'prenom' => 'Autre']);

        $service = app(ProfilSearchService::class);
        $resultats = $service->recherche(['q' => 'diop', 'tri' => 'pertinence'])->get();

        $this->assertCount(1, $resultats);
        $this->assertEquals('Diop', $resultats->first()->nom);
    }
}
