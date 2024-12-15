<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        if(config('app.env') === 'production') {
            \URL::forceScheme('https');
        }

        // Add this to ensure CSRF token is available
        view()->composer('*', function ($view) {
            if (!session()->has('_token')) {
                session()->regenerateToken();
            }
        });
    }
}
