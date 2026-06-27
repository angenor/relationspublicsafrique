<?php

declare(strict_types=1);

namespace Tests\Feature\Projets;

use App\Livewire\Projets\GrilleProjets;
use App\Models\Category;
use App\Models\Pays;
use App\Models\Projet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ListingTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_page_listing_repond_200(): void
    {
        Projet::factory()->create(['titre' => 'Projet visible']);

        $this->get('/projets')
            ->assertOk()
            ->assertSee('Nos projets', false);
    }

    public function test_un_projet_publie_est_liste_un_brouillon_non(): void
    {
        Projet::factory()->create(['titre' => 'Projet publié listé']);
        Projet::factory()->draft()->create(['titre' => 'Brouillon caché']);

        Livewire::test(GrilleProjets::class)
            ->assertSee('Projet publié listé')
            ->assertDontSee('Brouillon caché');
    }

    public function test_le_bouton_decouvrir_pointe_vers_le_detail(): void
    {
        $projet = Projet::factory()->create(['titre' => 'Projet à découvrir', 'slug' => 'projet-a-decouvrir']);

        Livewire::test(GrilleProjets::class)
            ->assertSee(route('projets.show', $projet->slug), false);
    }

    public function test_filtre_par_thematique(): void
    {
        $cat = Category::factory()->create(['type' => 'projet', 'slug' => 'theme-x', 'name' => 'Theme X', 'online' => 1]);
        $dans = Projet::factory()->create(['titre' => 'Projet thematique']);
        $dans->categories()->attach($cat->id, ['position' => 0]);
        Projet::factory()->create(['titre' => 'Projet hors theme']);

        Livewire::test(GrilleProjets::class)
            ->set('thematique', (string) $cat->id)
            ->assertSee('Projet thematique')
            ->assertDontSee('Projet hors theme');
    }

    public function test_filtre_par_statut(): void
    {
        Projet::factory()->statut('actif')->create(['titre' => 'Projet actif visible']);
        Projet::factory()->statut('realise')->create(['titre' => 'Projet realise cache']);

        Livewire::test(GrilleProjets::class)
            ->set('statut', 'actif')
            ->assertSee('Projet actif visible')
            ->assertDontSee('Projet realise cache');
    }

    public function test_filtre_par_zone(): void
    {
        $pays = Pays::factory()->create(['name' => 'Zonie', 'slug' => 'zonie']);
        Projet::factory()->withPays((int) $pays->id)->create(['titre' => 'Projet national']);
        Projet::factory()->regional()->create(['titre' => 'Projet regional']);

        Livewire::test(GrilleProjets::class)
            ->set('zone', 'pays:'.$pays->id)
            ->assertSee('Projet national')
            ->assertDontSee('Projet regional');
    }

    public function test_charger_plus_augmente_la_taille_de_page(): void
    {
        Projet::factory()->count(15)->create();

        Livewire::test(GrilleProjets::class)
            ->assertSet('perPage', 12)
            ->call('chargerPlus')
            ->assertSet('perPage', 24);
    }

    public function test_changer_un_filtre_ramene_per_page_au_pas_initial(): void
    {
        Projet::factory()->count(30)->create();

        Livewire::test(GrilleProjets::class)
            ->call('chargerPlus')          // perPage 12 -> 24
            ->assertSet('perPage', 24)
            ->set('statut', 'actif')       // changement de filtre -> reset au pas initial
            ->assertSet('perPage', 12);
    }

    public function test_etat_vide_quand_aucun_resultat(): void
    {
        Projet::factory()->create(['titre' => 'Un projet quelconque']);

        Livewire::test(GrilleProjets::class)
            ->set('q', 'zzz-introuvable-zzz')
            ->assertSee('Aucun projet ne correspond');
    }
}
