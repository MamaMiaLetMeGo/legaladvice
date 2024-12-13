<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class WelcomeController extends Controller
{
    public function newUser()
    {
        $categories = Category::all();
        return view('welcome.new-user', compact('categories'));
    }
}
