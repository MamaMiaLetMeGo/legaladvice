<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Category;

class PostController extends Controller
{
    /**
     * Display the specified post.
     */
    public function show(Post $post)
    {
        // Debug information
        Log::info('Showing post', [
            'post_id' => $post->id,
            'slug' => $post->slug,
            'status' => $post->status,
            'published_date' => $post->published_date,
            'is_published' => $post->isPublished(),
            'user_authenticated' => auth()->check()
        ]);

        // If post is published, show it
        if ($post->isPublished()) {
            $post->load(['author', 'categories']);

            $relatedPosts = Post::whereHas('categories', function ($query) use ($post) {
                $query->whereIn('categories.id', $post->categories->pluck('id'));
            })
            ->where('id', '!=', $post->id)
            ->published()
            ->latest('published_date')
            ->take(3)
            ->get();

            return view('posts.show', compact('post', 'relatedPosts'));
        }

        // If user is authenticated and can view unpublished posts
        if (auth()->check()) {
            // Check if user is admin or post author
            if (auth()->user()->id === $post->author_id || auth()->user()->is_admin) {
                $post->load(['author', 'categories']);

                $relatedPosts = Post::whereHas('categories', function ($query) use ($post) {
                    $query->whereIn('categories.id', $post->categories->pluck('id'));
                })
                ->where('id', '!=', $post->id)
                ->latest('published_date')
                ->take(3)
                ->get();

                return view('posts.show', [
                    'post' => $post,
                    'relatedPosts' => $relatedPosts,
                    'preview' => true
                ]);
            }
        }

        // If we get here, the post is not viewable
        abort(404, 'Post not found or not available.');
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.posts.create', compact('categories'));
    }
}