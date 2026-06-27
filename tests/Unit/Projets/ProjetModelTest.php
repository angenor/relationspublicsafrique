<?php

declare(strict_types=1);

namespace Tests\Unit\Projets;

use App\Models\Pays;
use App\Models\Projet;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjetModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_le_slug_est_genere_depuis_le_titre(): void
    {
        $projet = Projet::factory()->create(['titre' => 'Mon Projet Génial', 'slug' => null]);

        $this->assertSame('mon-projet-genial', $projet->slug);
    }

    public function test_le_titre_normalise_est_sans_accents(): void
    {
        $projet = Projet::factory()->create(['titre' => 'Élection présidentielle à Abidjan']);

        $this->assertSame('election presidentielle a abidjan', $projet->titre_normalise);
    }

    public function test_les_casts(): void
    {
        $projet = Projet::factory()->create([
            'is_published' => true,
            'featured' => true,
            'position' => 3,
        ]);

        $this->assertIsBool($projet->is_published);
        $this->assertIsBool($projet->featured);
        $this->assertSame(3, $projet->position);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $projet->published_at);
    }

    public function test_un_projet_publie_sans_date_recoit_une_date_de_publication(): void
    {
        $projet = Projet::factory()->create(['is_published' => true, 'published_at' => null]);

        $this->assertNotNull($projet->published_at);
        $this->assertTrue($projet->published_at->isPast() || $projet->published_at->isCurrentMinute());
    }

    public function test_le_scope_published_exclut_brouillons_et_dates_futures(): void
    {
        Projet::factory()->create(['is_published' => true, 'published_at' => now()->subDay()]);
        Projet::factory()->draft()->create();
        Projet::factory()->scheduled()->create(); // publié mais published_at futur

        $this->assertSame(1, Projet::published()->count());
    }

    public function test_le_scope_by_statut(): void
    {
        Projet::factory()->statut('actif')->create();
        Projet::factory()->statut('realise')->create();

        $this->assertSame(1, Projet::byStatut('actif')->count());
    }

    public function test_le_scope_by_zone_pays_et_portee(): void
    {
        $national = Projet::factory()->withPays(42)->create();
        $regional = Projet::factory()->regional()->create();

        $this->assertTrue(Projet::byZone('pays:42')->pluck('id')->contains($national->id));
        $this->assertFalse(Projet::byZone('pays:42')->pluck('id')->contains($regional->id));

        $this->assertTrue(Projet::byZone('portee:regional')->pluck('id')->contains($regional->id));
        $this->assertFalse(Projet::byZone('portee:regional')->pluck('id')->contains($national->id));
    }

    public function test_le_scope_ordered_priorise_featured_puis_position(): void
    {
        $normal = Projet::factory()->create(['featured' => false, 'position' => 1, 'published_at' => now()->subDays(2)]);
        $star = Projet::factory()->create(['featured' => true, 'position' => 5, 'published_at' => now()->subDays(10)]);

        $this->assertSame($star->id, Projet::ordered()->first()->id);
    }

    public function test_accessor_statut_label(): void
    {
        $projet = Projet::factory()->statut('realise')->create();

        $this->assertSame('Réalisé', $projet->statut_label);
    }

    public function test_accessor_zone_label_pays_vs_libelle(): void
    {
        $pays = Pays::factory()->create(['name' => 'Sénégal', 'slug' => 'senegal']);
        $national = Projet::factory()->withPays((int) $pays->id)->create();
        $regional = Projet::factory()->regional('Afrique centrale')->create();

        $this->assertSame('Sénégal', $national->fresh()->zone_label);
        $this->assertSame('Afrique centrale', $regional->zone_label);
    }

    public function test_relations_enfants(): void
    {
        $projet = Projet::factory()->create();
        $projet->resultats()->create(['libelle' => 'Bénéficiaires', 'valeur' => '100', 'position' => 0]);
        $projet->medias()->create(['type' => 'image', 'chemin' => 'projets/galerie/x.jpg', 'position' => 0]);
        $projet->temoignages()->create(['auteur' => 'X', 'contenu' => 'Super', 'position' => 0]);

        $this->assertCount(1, $projet->resultats);
        $this->assertCount(1, $projet->medias);
        $this->assertCount(1, $projet->temoignages);
    }
}
