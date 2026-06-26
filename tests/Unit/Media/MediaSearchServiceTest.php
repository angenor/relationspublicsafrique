<?php

declare(strict_types=1);

namespace Tests\Unit\Media;

use App\Models\Category;
use App\Models\Media;
use App\Models\Pays;
use App\Services\Media\MediaSearchService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MediaSearchServiceTest extends TestCase
{
    use RefreshDatabase;

    private function service(): MediaSearchService
    {
        return new MediaSearchService;
    }

    public function test_recherche_par_titre_insensible_aux_accents(): void
    {
        $match = Media::factory()->published()->create(['titre' => 'Élection présidentielle']);
        $autre = Media::factory()->published()->create(['titre' => 'Sujet économique divers']);

        $ids = $this->service()->recherche(['q' => 'election'])->pluck('id');

        $this->assertTrue($ids->contains($match->id));
        $this->assertFalse($ids->contains($autre->id));
    }

    public function test_filtre_par_type(): void
    {
        $podcast = Media::factory()->published()->ofType('podcast')->create();
        $article = Media::factory()->published()->ofType('article')->create();

        $ids = $this->service()->recherche(['type' => 'podcast'])->pluck('id');

        $this->assertTrue($ids->contains($podcast->id));
        $this->assertFalse($ids->contains($article->id));
    }

    public function test_filtre_par_categorie(): void
    {
        $cat = Category::factory()->create(['type' => 'media', 'slug' => 'thematique-test', 'name' => 'Thématique test']);
        $dans = Media::factory()->published()->create();
        $dans->categories()->attach($cat->id, ['position' => 0]);
        $hors = Media::factory()->published()->create();

        $ids = $this->service()->recherche(['categorie' => 'thematique-test'])->pluck('id');

        $this->assertTrue($ids->contains($dans->id));
        $this->assertFalse($ids->contains($hors->id));
    }

    public function test_filtre_par_pays(): void
    {
        $pays = Pays::factory()->create(['name' => 'Testland', 'slug' => 'testland']);
        $dans = Media::factory()->published()->create(['pays_id' => $pays->id]);
        $hors = Media::factory()->published()->create(['pays_id' => null]);

        $ids = $this->service()->recherche(['pays' => 'testland'])->pluck('id');

        $this->assertTrue($ids->contains($dans->id));
        $this->assertFalse($ids->contains($hors->id));
    }

    public function test_tri_populaire(): void
    {
        Media::factory()->published()->create(['popularity_score' => 1]);
        $fort = Media::factory()->published()->create(['popularity_score' => 999]);

        $this->assertSame($fort->id, $this->service()->recherche(['tri' => 'populaire'])->first()->id);
    }

    public function test_combinaison_de_filtres_est_un_et_logique(): void
    {
        $cat = Category::factory()->create(['type' => 'media', 'slug' => 'comm', 'name' => 'Comm']);

        $bon = Media::factory()->published()->ofType('podcast')->create();
        $bon->categories()->attach($cat->id, ['position' => 0]);

        $mauvaisType = Media::factory()->published()->ofType('article')->create();
        $mauvaisType->categories()->attach($cat->id, ['position' => 0]);

        $mauvaiseCat = Media::factory()->published()->ofType('podcast')->create();

        $ids = $this->service()->recherche(['type' => 'podcast', 'categorie' => 'comm'])->pluck('id');

        $this->assertTrue($ids->contains($bon->id));
        $this->assertFalse($ids->contains($mauvaisType->id));
        $this->assertFalse($ids->contains($mauvaiseCat->id));
    }

    public function test_seuls_les_contenus_publies_sont_retournes(): void
    {
        Media::factory()->published()->create();
        Media::factory()->draft()->create();
        Media::factory()->scheduled()->create();

        $this->assertSame(1, $this->service()->recherche([])->count());
    }
}
