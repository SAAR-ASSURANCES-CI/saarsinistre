<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Enregistrer OrangeService comme singleton pour garantir une configuration SSL cohérente
        $this->app->singleton(\App\Services\OrangeService::class, function ($app) {
            return new \App\Services\OrangeService();
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
