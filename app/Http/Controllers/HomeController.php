<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        $posts = Post::with(['author', 'categories'])
            ->published()
            ->latest('published_date')
            ->take(6)
            ->get();

        return view('home', compact('posts'));
    }
}
