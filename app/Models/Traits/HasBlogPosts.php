<?php

namespace App\Models\Traits;

use App\Models\Post;

trait HasBlogPosts
{
    public function posts()
    {
        return $this->hasMany(Post::class, 'author_id');
    }

    public function publishedPosts()
    {
        return $this->posts()->published();
    }

    public function draftPosts()
    {
        return $this->posts()->draft();
    }

    public function getPostCountAttribute()
    {
        return $this->posts()->count();
    }

    public function getPublishedPostCountAttribute()
    {
        return $this->publishedPosts()->count();
    }
}