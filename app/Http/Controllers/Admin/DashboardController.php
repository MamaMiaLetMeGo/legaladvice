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

        // Get recent posts
        $recentPosts = Post::with(['author', 'categories'])
            ->latest()
            ->take(5)
            ->get();

        // Get monthly post counts for chart - PostgreSQL version
        $monthlyPosts = Post::select(
            DB::raw("to_char(created_at, 'YYYY-MM') as month"),
            DB::raw('count(*) as total')
        )
            ->groupBy('month')
            ->orderBy('month', 'desc')
            ->take(12)
            ->get();

        return view('admin.dashboard', compact(
            'postStats',
            'recentPosts',
            'monthlyPosts'
        ));
    }
}