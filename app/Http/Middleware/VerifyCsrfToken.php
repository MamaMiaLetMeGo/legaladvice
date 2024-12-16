<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        'pusher/auth', // Allow Pusher authentication endpoint
        'broadcasting/auth', // Allow Laravel broadcasting authentication
        '/chat/send-message' // Add our new chat endpoint
    ];
} 