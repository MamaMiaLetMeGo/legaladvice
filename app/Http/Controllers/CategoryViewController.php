<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryViewController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index()
    {
        $categories = Category::withCount('posts')
            ->orderBy('name')
            ->paginate(12);
        
        return view('categories.index', compact('categories'));
    }

    /**
     * Display the specified category.
     */
    public function show($slug)
    {
        $category = Category::where('slug', $slug)
            ->firstOrFail();
        
        $posts = $category->posts()
            ->with(['author'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        return view('categories.show', compact('category', 'posts'));
    }
}