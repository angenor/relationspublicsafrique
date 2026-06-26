<?php

namespace App\Providers;

use App\Models\Media;
use App\Models\Profil;
use App\Observers\MediaObserver;
use App\Observers\ProfilObserver;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\RateLimiter;
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
                file_put_contents('php://stdout', "\e[ {$executed->sql}\t\e[ ".json_encode($executed->bindings)."\n");
            });
        }
        Paginator::useBootstrap('');
        if ($this->app->environment('production')) {
            \URL::forceScheme('https');
        }

        Profil::observe(ProfilObserver::class);
        Media::observe(MediaObserver::class);

        // Throttling des formulaires publics média (commentaire, newsletter).
        RateLimiter::for('media-public', fn (Request $request) => Limit::perMinute(
            (int) config('media.rate_limit_public', 30)
        )->by($request->ip()));
    }
}
