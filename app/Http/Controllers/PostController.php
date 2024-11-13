<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class PostController extends Controller
{
    // Display a listing of posts
    public function index()
    {
        $posts = Post::latest('published_date')->get();
        return view('posts.index', compact('posts'));
    }

    // Show the form for creating a new post
    public function create()
    {
        return view('admin.posts.create');
    }

    // Store a newly created post in the database
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required',
            'author' => 'required',
            'body_content' => 'required',
            'slug' => 'required|unique:posts',
        ]);

        $post = new Post;
        $post->title = $request->title;
        $post->author = $request->author;
        $post->breadcrumb = $request->breadcrumb;
        $post->body_content = $request->body_content;
        $post->featured_image = $request->featured_image;
        $post->categories = $request->categories;
        $post->slug = $request->slug;
        $post->video_url = $request->video_url;
        $post->published_date = $request->published_date;
        $post->save();

        return redirect()->route('admin.posts.index')->with('success', 'Post created successfully');
    }

    // Display the specified post
    public function show($slug)
    {
        $post = Post::where('slug', $slug)->firstOrFail();
        return view('posts.show', compact('post'));
    }

    // Show the form for editing the specified post
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        return view('admin.posts.edit', compact('post'));
    }

    // Update the specified post in the database
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'author' => 'required',
            'body_content' => 'required',
            'slug' => 'required|unique:posts,slug,' . $id,
        ]);

        $post = Post::findOrFail($id);
        $post->title = $request->title;
        $post->author = $request->author;
        $post->breadcrumb = $request->breadcrumb;
        $post->body_content = $request->body_content;
        $post->featured_image = $request->featured_image;
        $post->categories = $request->categories;
        $post->slug = $request->slug;
        $post->video_url = $request->video_url;
        $post->save();

        return redirect()->route('admin.posts.index')->with('success', 'Post updated successfully');
    }

    // Remove the specified post from the database
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();

        return redirect()->route('admin.posts.index')->with('success', 'Post deleted successfully');
    }

    // Display a list of posts for the admin
    public function adminIndex()
    {
        $posts = Post::all();
        return view('admin.posts.index', compact('posts'));
    }
}
