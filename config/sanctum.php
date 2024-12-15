<?php

use Laravel\Sanctum\Sanctum;

return [
    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    |
    | Listed domains will receive stateful API authentication cookies.
    | This should include your local and production domains which
    | access your API via a frontend SPA or mobile application.
    |
    */

    'stateful' => [
        '127.0.0.1',
        '127.0.0.1:8000',
        'localhost',
        'localhost:8000',
        parse_url(env('APP_URL'), PHP_URL_HOST) ?? 'localhost',
    ],

    /*
    |--------------------------------------------------------------------------
    | Sanctum Guards
    |--------------------------------------------------------------------------
    |
    | Authentication guards Sanctum will use for incoming requests.
    | If none can authenticate the request, Sanctum will use the
    | bearer token present in the authorization header.
    |
    */

    'guard' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Expiration Minutes
    |--------------------------------------------------------------------------
    |
    | Time in minutes before tokens expire. Null means tokens never expire.
    | This overrides any "expires_at" attribute on the token itself.
    |
    */

    'expiration' => null,

    /*
    |--------------------------------------------------------------------------
    | Token Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix for token generation, useful for security scanning tools
    | that detect hardcoded tokens in source code repositories.
    |
    */

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', ''),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Middleware
    |--------------------------------------------------------------------------
    |
    | Middleware used when processing SPA authentication. Customize
    | these if needed for your specific application setup.
    |
    */

    'middleware' => [
        'verify_csrf_token' => \App\Http\Middleware\VerifyCsrfToken::class,
        'encrypt_cookies' => \App\Http\Middleware\EncryptCookies::class,
    ],
]; 