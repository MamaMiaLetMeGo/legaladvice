<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Category;
use Illuminate\Http\Request;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['author', 'categories'])
            ->where('status', 'published')
            ->latest('published_date')
            ->paginate(12);

        return view('posts.index', compact('posts'));
    }

    public function show(Post $post)
    {
        if ($post->status !== 'published' && !auth()->user()?->can('update', $post)) {
            abort(404);
        }

        $relatedPosts = Post::where('status', 'published')
            ->where('id', '!=', $post->id)
            ->whereHas('categories', function ($query) use ($post) {
                $query->whereIn('id', $post->categories->pluck('id'));
            })
            ->latest('published_date')
            ->take(3)
            ->get();

        return view('posts.show', compact('post', 'relatedPosts'));
    }

    public function category(Category $category)
    {
        $posts = Post::with(['author', 'categories'])
            ->where('status', 'published')
            ->whereHas('categories', function ($query) use ($category) {
                $query->where('id', $category->id);
            })
            ->latest('published_date')
            ->paginate(12);

        return view('posts.index', compact('posts', 'category'));
    }
}