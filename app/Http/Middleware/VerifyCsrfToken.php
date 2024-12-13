<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        // If needed, you can exclude certain routes from CSRF protection
        // '/api/chat/send',
        'pusher/auth', // Allow Pusher authentication endpoint
        'broadcasting/auth' // Allow Laravel broadcasting authentication
    ];
} 