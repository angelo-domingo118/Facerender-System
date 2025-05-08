<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\CompositeFeaturesService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
        
        $this->app->singleton(CompositeFeaturesService::class, function ($app) {
            return new CompositeFeaturesService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
