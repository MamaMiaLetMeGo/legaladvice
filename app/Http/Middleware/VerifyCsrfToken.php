<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    protected $except = [
        // Add any routes that should be excluded from CSRF verification
    ];

    protected function tokensMatch($request)
    {
        $token = $request->input('_token') ?: $request->header('X-CSRF-TOKEN');

        if (!$token && $request->header('X-XSRF-TOKEN')) {
            $token = $this->encrypter->decrypt($request->header('X-XSRF-TOKEN'), static::serialized());
        }

        return is_string($token) && 
               is_string($request->session()->token()) &&
               hash_equals($request->session()->token(), $token);
    }
} 