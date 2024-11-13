<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Post;
use App\Policies\AuthorPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        User::class => AuthorPolicy::class,
        Post::class => AuthorPolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();
    }
}