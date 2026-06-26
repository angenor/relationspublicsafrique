<?php

declare(strict_types=1);

namespace Tests\Feature\Media;

use App\Models\Media;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class MediaPublicTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_home_affiche_les_rubriques(): void
    {
        Media::factory()->published()->featured()->create(['titre' => 'Le contenu vedette']);
        Media::factory()->published()->create(['titre' => 'Une derniere publication']);
        Media::factory()->published()->pinned()->create(['titre' => 'La selection de la redaction']);

        $response = $this->get('/media');

        $response->assertOk();
        $response->assertSee('À la une', false);
        $response->assertSee('Dernières publications', false);
        $response->assertSee('Tendances', false);
        $response->assertSee('Le contenu vedette', false);
    }

    public function test_le_detail_d_un_contenu_publie_repond_200(): void
    {
        $media = Media::factory()->published()->create([
            'titre' => 'Article public lisible',
            'slug' => 'article-public-lisible',
        ]);

        $this->get($media->link)
            ->assertOk()
            ->assertSee('Article public lisible', false);
    }

    public function test_le_detail_incremente_les_vues(): void
    {
        $media = Media::factory()->published()->create(['view' => 5]);

        $this->get($media->link)->assertOk();

        $this->assertSame(6, (int) $media->fresh()->view);
    }

    public function test_un_contenu_non_publie_renvoie_404(): void
    {
        $brouillon = Media::factory()->draft()->create();
        $programme = Media::factory()->scheduled()->create();
        $archive = Media::factory()->archived()->create();

        foreach ([$brouillon, $programme, $archive] as $media) {
            $this->get('/media/'.$media->slug.'-'.$media->id)->assertNotFound();
        }
    }

    public function test_non_regression_routes_existantes_inchangees(): void
    {
        // FR-041/SC-008 : aucune route existante n'est modifiée ni shadowée.
        $this->assertSame(
            'App\Http\Controllers\PostController@index',
            Route::getRoutes()->getByName('accueil')->getActionName(),
        );
        $this->assertSame(
            'App\Http\Controllers\PostController@blog',
            Route::getRoutes()->getByName('blog')->getActionName(),
        );
        $this->assertSame(
            'App\Http\Controllers\PostController@shoqBlog',
            Route::getRoutes()->getByName('blog.show')->getActionName(),
        );

        // Les nouvelles routes média pointent bien vers MediaController.
        $this->assertSame(
            'App\Http\Controllers\MediaController@home',
            Route::getRoutes()->getByName('media.home')->getActionName(),
        );
        $this->assertSame(
            'App\Http\Controllers\MediaController@show',
            Route::getRoutes()->getByName('media.show')->getActionName(),
        );

        // La liste des actualités continue de répondre.
        $this->get('/actualites')->assertOk();
    }
}
