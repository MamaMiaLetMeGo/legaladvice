<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsLawyer
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Add logging to debug
        \Log::info('User role check:', [
            'user' => $request->user(),
            'is_lawyer' => $request->user()?->is_lawyer,
            'role' => $request->user()?->role
        ]);

        // Check both is_lawyer boolean and role field for compatibility
        if (!$request->user() || (!$request->user()->is_lawyer && $request->user()->role !== 'lawyer')) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Unauthorized. Lawyer access required.'], 403);
            }
            return redirect()->route('home')->with('error', 'Unauthorized. Lawyer access required.');
        }

        return $next($request);
    }
}
