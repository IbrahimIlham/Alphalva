<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Vite;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Debug log, cek apakah method ini aktif
        logger('Custom Vite manifest path loaded');

        // Override path ke manifest.json
        $this->app->bind(Vite::class, function () {
            return new Vite(
                base_path('public_html/build/manifest.json'),
                ['resources/js/app.js', 'resources/css/app.css']
            );
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
