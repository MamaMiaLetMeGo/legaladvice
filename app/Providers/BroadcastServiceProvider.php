<?php

namespace App\Providers;

use Illuminate\Support\Facades\Broadcast;
use Illuminate\Support\ServiceProvider;

class BroadcastServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // This loads your channel routes file
        Broadcast::routes();

        // This loads your channels.php file where you define your channel authorization logic
        require base_path('routes/channels.php');
    }
}