<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuthorPolicy
{
    use HandlesAuthorization;

    public function updateProfile(User $user, User $author): Response
    {
        return $user->id === $author->id
            ? Response::allow()
            : Response::deny('You can only edit your own profile.');
    }

    public function deletePost(User $user, Post $post): Response
    {
        return $user->id === $post->author_id
            ? Response::allow()
            : Response::deny('You can only delete your own posts.');
    }

    public function createPost(User $user): Response
    {
        // Add any conditions for who can create posts
        return Response::allow();
    }

    public function updatePost(User $user, Post $post): Response
    {
        return $user->id === $post->author_id
            ? Response::allow()
            : Response::deny('You can only edit your own posts.');
    }
}