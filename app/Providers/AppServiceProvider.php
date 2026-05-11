<?php

namespace App\Providers;

use App\Models\Profil;
use App\Observers\ProfilObserver;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {

        /* if ($this->app->environment() !== 'production') {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        } */

        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->environment() === 'local') {
            \DB::listen(function (QueryExecuted $executed) {
                file_put_contents('php://stdout', json_encode($executed->bindings));
                file_put_contents('php://stdout',  "\e[ {$executed->sql}\t\e[ " . json_encode($executed->bindings) . "\n");
            });
        }
        Paginator::useBootstrap("");
        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }

        Profil::observe(ProfilObserver::class);
    }
}
