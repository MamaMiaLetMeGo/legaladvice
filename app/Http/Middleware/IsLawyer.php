<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class IsLawyer
{
    public function handle(Request $request, Closure $next): Response
    {
        Log::info('IsLawyer middleware handling request', [
            'path' => $request->path(),
            'user' => auth()->user()?->email,
            'is_lawyer' => auth()->user()?->is_lawyer
        ]);

        if (!$request->user() || !$request->user()->is_lawyer) {
            return redirect()->route('home')->with('error', 'Access denied. Lawyer privileges required.');
        }

        return $next($request);
    }
}
