<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CategoryViewController extends Controller
{
   public function index()
   {
       $categories = Category::withCount(['posts' => function($query) {
               $query->published();
           }])
           ->when(request('sort'), function($query) {
               if (request('sort') === 'posts') {
                   $query->orderByDesc('posts_count');
               }
           }, function($query) {
               $query->orderBy('name');
           })
           ->paginate(12)
           ->withQueryString();
       
       return view('categories.index', compact('categories'));
   }

   public function show(Category $category)
   {
       $posts = $category->posts()
           ->with(['author', 'categories'])
           ->published()
           ->latest('published_date')
           ->paginate(12);
       
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

   public function search(Request $request)
   {
       $query = $request->get('q');
       
       $categories = Category::where(function($q) use ($query) {
           if ($query) {
               $q->where('name', 'ILIKE', "%{$query}%")
                 ->orWhere('description', 'ILIKE', "%{$query}%");
           }
       })
       ->withCount(['posts' => function($query) {
           $query->published();
       }])
       ->orderBy('name')
       ->paginate(12)
       ->withQueryString();
       
       return view('categories.index', compact('categories', 'query'));
   }
}