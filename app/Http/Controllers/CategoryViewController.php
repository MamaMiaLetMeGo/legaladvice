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
        $categories = Category::withCount(['posts' => function($query) {
                $query->published();
            }])
            ->has('posts') // Only show categories with posts
            ->when(request('sort'), function($query) {
                if (request('sort') === 'posts') {
                    $query->orderByDesc('posts_count');
                }
            }, function($query) {
                $query->orderBy('name');
            })
            ->paginate(12)
            ->withQueryString(); // Keep sorting in pagination links
        
        return view('categories.index', compact('categories'));
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category) // Using route model binding
    {
        $posts = $category->posts()
            ->with(['author', 'categories']) // Eager load relationships
            ->published() // Only show published posts
            ->latest('published_date') // Order by publish date
            ->paginate(12);
        
        // Get related categories based on posts in this category
        $relatedCategories = Category::whereHas('posts', function($query) use ($category) {
                $query->whereIn('posts.id', $category->posts->pluck('id'));
            })
            ->where('id', '!=', $category->id)
            ->withCount(['posts' => function($query) {
                $query->published();
            }])
            ->orderByDesc('posts_count')
            ->limit(5)
            ->get();
        
        return view('categories.show', compact('category', 'posts', 'relatedCategories'));
    }

    /**
     * Search categories.
     */
    public function search(Request $request)
    {
        $query = $request->get('q');
        
        $categories = Category::where('name', 'ILIKE', "%{$query}%")
            ->orWhere('description', 'ILIKE', "%{$query}%")
            ->withCount(['posts' => function($query) {
                $query->published();
            }])
            ->has('posts')
            ->orderBy('name')
            ->paginate(12)
            ->withQueryString();
        
        return view('categories.index', compact('categories', 'query'));
    }
}