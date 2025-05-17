<?php

namespace App\Providers;

use Illuminate\Support\Facades\Vite;
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
        // Enable Vite prefetch with concurrency control
        Vite::prefetch(concurrency: 3);

        // Optional: support hot module replacement during local dev
        if ($this->app->environment('local')) {
            Vite::useHotFile(public_path('hot'));
        }
    }
}
