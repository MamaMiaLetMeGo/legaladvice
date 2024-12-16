<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;

class LawyerMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();
        
        Log::info('LawyerMiddleware check', [
            'user_id' => $user?->id,
            'email' => $user?->email,
            'is_lawyer' => $user?->is_lawyer ?? false,
            'is_admin' => $user?->role === 'admin',
            'role' => $user?->role,
            'path' => $request->path()
        ]);

        if (!$user) {
            Log::warning('No authenticated user');
            return redirect()->route('login');
        }

        // Debug the condition
        $isAuthorized = $user->is_lawyer || $user->role === 'admin';
        Log::info('Authorization check', [
            'is_lawyer' => $user->is_lawyer,
            'role' => $user->role,
            'is_authorized' => $isAuthorized
        ]);

        if ($isAuthorized) {
            return $next($request);
        }

        Log::warning('Access denied', [
            'user_id' => $user->id,
            'is_lawyer' => $user->is_lawyer,
            'role' => $user->role
        ]);

        return redirect()->route('home')
            ->with('error', 'You do not have access to this area.');
    }
} 