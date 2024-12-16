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
        \Log::info('IsAdmin middleware check', [
            'user' => $request->user()?->email,
            'is_admin' => $request->user()?->role === 'admin',
            'role' => $request->user()?->role,
            'path' => $request->path()
        ]);

        if (!$request->user() || $request->user()->role !== 'admin') {
            \Log::warning('Unauthorized admin access attempt', [
                'user' => $request->user()?->email,
                'path' => $request->path()
            ]);
            return redirect()->route('home');
        }

        return $next($request);
    }
}
