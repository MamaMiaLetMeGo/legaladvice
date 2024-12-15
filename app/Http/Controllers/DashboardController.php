<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        if ($request->user()->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        if ($request->user()->is_lawyer) {
            return redirect()->route('lawyer.dashboard');
        }

        // For regular users, redirect to home or show a default dashboard
        return redirect()->route('home');
    }
} 