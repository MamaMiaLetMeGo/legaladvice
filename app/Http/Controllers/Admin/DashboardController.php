<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Get post statistics
        $postStats = [
            'total' => Post::count(),
            'published' => Post::published()->count(),
            'drafts' => Post::draft()->count(),
            'thisMonth' => Post::whereMonth('created_at', Carbon::now()->month)->count(),
        ];

        // Get category statistics
        $categoryStats = Category::withCount('posts')->get();

        // Get recent posts
        $recentPosts = Post::with(['author', 'categories'])
            ->latest()
            ->take(5)
            ->get();

        // Get monthly post counts for chart
        $monthlyPosts = Post::select(
            DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'),
            DB::raw('count(*) as total')
        )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        return view('admin.dashboard', compact(
            'postStats',
            'categoryStats',
            'recentPosts',
            'monthlyPosts'
        ));
    }
}