<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Keep your existing sanctum route
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});