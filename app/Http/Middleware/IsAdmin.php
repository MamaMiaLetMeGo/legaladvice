<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class IsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('IsAdmin middleware check', [
            'user' => $request->user()?->email,
            'role' => $request->user()?->role,
            'isAdmin' => $request->user()?->isAdmin()
        ]);

        if (!$request->user() || !$request->user()->isAdmin()) {
            Log::warning('Unauthorized admin access attempt', [
                'user' => $request->user()?->email,
                'path' => $request->path()
            ]);
            abort(403, 'Unauthorized access.');
        }

        return $next($request);
    }
}
