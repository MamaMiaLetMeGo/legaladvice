<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        //
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     *
     * @throws \Illuminate\Session\TokenMismatchException
     */
    public function handle($request, Closure $next)
    {
        try {
            return parent::handle($request, $next);
        } catch (TokenMismatchException $e) {
            // If it's an AJAX request, return JSON response
            if ($request->ajax()) {
                return response()->json([
                    'error' => 'CSRF token mismatch',
                    'code' => 'csrf_token_mismatch'
                ], 419);
            }

            // Otherwise, redirect back with error
            return redirect()->back()->with('error', 'Your session has expired. Please refresh the page.');
        }
    }
}