<?php

namespace App\Providers;

use App\Console\Kernel;
use Illuminate\Support\ServiceProvider;

class ConsoleServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind our custom console kernel
        $this->app->singleton(
            \Illuminate\Contracts\Console\Kernel::class,
            Kernel::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
