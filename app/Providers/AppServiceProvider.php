<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Ensure routes are registered if a RouteServiceProvider is missing.
        // This will load `routes/api.php` with the `api` middleware and `routes/web.php` with `web`.
        if (file_exists(base_path('routes/api.php'))) {
            Route::middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));
        }

        if (file_exists(base_path('routes/web.php'))) {
            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        }
    }
}
