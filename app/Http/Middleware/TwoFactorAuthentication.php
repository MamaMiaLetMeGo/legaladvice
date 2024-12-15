<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TwoFactorAuthentication
{
    public function handle(Request $request, Closure $next)
    {
        Log::info('TwoFactorAuthentication middleware handling request', [
            'path' => $request->path(),
            'user' => $request->user()?->email
        ]);

        $user = $request->user();

        // Skip 2FA check for these routes
        $excludedRoutes = [
            'login',
            'logout',
            '2fa.challenge',
            '2fa.verify',
            '2fa.recovery',
            '2fa.recovery.store'
        ];

        if (in_array($request->route()->getName(), $excludedRoutes)) {
            return $next($request);
        }

        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->two_factor_enabled && !session('two_factor_confirmed')) {
            // Store intended URL in session
            session()->put('url.intended', $request->url());
            
            // Store CSRF token in session
            session()->put('_token', csrf_token());
            
            // Ensure session is saved before redirect
            session()->save();

            return redirect()->route('2fa.challenge');
        }

        return $next($request);
    }
}
