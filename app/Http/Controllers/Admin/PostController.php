<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PostController extends Controller
{
    /**
     * Display a listing of the posts.
     */
    public function index(Request $request)
    {
        $query = Post::with(['author', 'categories'])
            ->latest();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->whereHas('categories', function ($q) use ($request) {
                $q->where('id', $request->category);
            });
        }

        // Search functionality
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                  ->orWhere('body_content', 'like', "%{$request->search}%");
            });
        }

        $posts = $query->paginate(10);
        $categories = Category::orderBy('name')->get();

        return view('admin.posts.index', compact('posts', 'categories'));
    }

    /**
     * Show the form for creating a new post.
     */
    public function create()
    {
        $categories = Category::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        return view('admin.posts.create', compact('categories', 'users'));
    }

    /**
     * Store a newly created post in storage.
     */
    public function store(Request $request)
    {
    $validated = $request->validate([
        'title' => 'required',
        'slug' => 'required|unique:posts',
        'body_content' => 'required',
        'categories' => 'required|array',
        'categories.*' => 'exists:categories,id',
        'author_id' => 'required|exists:users,id'
    ]);

    $post = Post::create([
        'title' => $validated['title'],
        'slug' => $validated['slug'],
        'body_content' => $validated['body_content'],
        'author_id' => $validated['author_id'],
        'breadcrumb' => $request->breadcrumb,
        'featured_image' => $request->featured_image,
        'video_url' => $request->video_url,
        'published_date' => $request->published_date,
        'status' => 'draft',
    ]);

    $post->categories()->attach($request->categories);

    return redirect()->route('admin.posts.index')
        ->with('success', 'Post created successfully.');
    }

    /**
     * Show the form for editing the specified post.
     */
    public function edit(Post $post)
        {
            if (! Gate::allows('update', $post)) {
                abort(403);
        }
        
        $categories = Category::orderBy('name')->get();
        $users = User::orderBy('name')->get();
        return view('admin.posts.edit', compact('post', 'categories', 'users'));
        }

    public function update(Request $request, Post $post)
    {
        if (! Gate::allows('update', $post)) {
            abort(403);
        }
    
        // If not admin, force author_id to be current user
        if (!auth()->user()->is_admin) {
            $request->merge(['author_id' => auth()->id()]);
        }
            
        $validated = $request->validate([
            'title' => 'required',
            'body_content' => 'required',
            'slug' => 'required|unique:posts,slug,' . $post->id,
            'author_id' => 'required|exists:users,id'
        ]);
    
        $post->update([
            'title' => $validated['title'],
            'slug' => $validated['slug'],
            'body_content' => $validated['body_content'],
            'author_id' => $validated['author_id'],
            'breadcrumb' => $request->breadcrumb,
            'featured_image' => $request->featured_image,
            'video_url' => $request->video_url,
            'published_date' => $request->published_date,
        ]);
    
        if ($request->has('categories')) {
            $post->categories()->sync($request->categories);
        }
    
        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post updated successfully');
    }

    /**
     * Remove the specified post from storage.
     */
    public function destroy(Post $post)
    {
        $post->categories()->detach();
        $post->delete();

        return redirect()
            ->route('admin.posts.index')
            ->with('success', 'Post deleted successfully');
    }

    public function publish(Post $post)
    {
        $post->update([
            'status' => 'published',
            'published_date' => $post->published_date ?? now()
        ]);

        return back()->with('success', 'Post published successfully.');
    }

    public function unpublish(Post $post)
    {
        $post->update([
            'status' => 'draft',
            'published_date' => null
        ]);

        return back()->with('success', 'Post unpublished successfully.');
    }

    public function archive(Post $post)
    {
        $post->update([
            'status' => 'archived'
        ]);

        return back()->with('success', 'Post archived successfully.');
    }
}