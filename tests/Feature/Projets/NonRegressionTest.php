<?php

declare(strict_types=1);

namespace Tests\Feature\Projets;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

/**
 * Garantit que la section Projets n'altère aucune route/surface existante
 * (FR-024/SC-008 du périmètre : intégration strictement additive).
 */
class NonRegressionTest extends TestCase
{
    use RefreshDatabase;

    public function test_les_pages_existantes_repondent_toujours(): void
    {
        $this->get('/actualites')->assertOk();
        $this->get('/media')->assertOk();
        $this->get('/annuaire/consulter')->assertOk();
    }

    public function test_les_actions_des_routes_existantes_sont_inchangees(): void
    {
        $this->assertSame(
            'App\Http\Controllers\PostController@index',
            Route::getRoutes()->getByName('accueil')->getActionName(),
        );
        $this->assertSame(
            'App\Http\Controllers\PostController@blog',
            Route::getRoutes()->getByName('blog')->getActionName(),
        );
        $this->assertSame(
            'App\Http\Controllers\MediaController@home',
            Route::getRoutes()->getByName('media.home')->getActionName(),
        );
        $this->assertSame(
            'App\Http\Controllers\PostController@contact',
            Route::getRoutes()->getByName('contact')->getActionName(),
        );
    }

    public function test_les_nouvelles_routes_projets_pointent_vers_le_bon_controleur(): void
    {
        $this->assertSame(
            'App\Http\Controllers\ProjetController@index',
            Route::getRoutes()->getByName('projets.index')->getActionName(),
        );
        $this->assertSame(
            'App\Http\Controllers\ProjetController@show',
            Route::getRoutes()->getByName('projets.show')->getActionName(),
        );
    }

    public function test_la_page_projets_repond_200(): void
    {
        $this->get('/projets')->assertOk();
    }
}
