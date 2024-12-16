<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CommentController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Middleware\IsAdmin;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', IsAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        
        // Posts Management
        Route::resource('posts', PostController::class);
        Route::post('/upload-video', [PostController::class, 'videoUpload'])->name('video.upload');
        Route::post('/upload-image', [PostController::class, 'uploadImages'])->name('image.upload');
        Route::post('/posts/{post}/publish', [PostController::class, 'publish'])->name('posts.publish');
        Route::post('/posts/{post}/unpublish', [PostController::class, 'unpublish'])->name('posts.unpublish');
        Route::post('/posts/{post}/archive', [PostController::class, 'archive'])->name('posts.archive');

        // Category Management - Order is important here
        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::get('/categories/create', [CategoryController::class, 'create'])->name('categories.create');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::get('/categories/{category}/edit', [CategoryController::class, 'edit'])->name('categories.edit');
        Route::put('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
        Route::post('/categories/upload-image', [CategoryController::class, 'uploadImages'])->name('categories.image.upload');
        Route::post('/categories/{category}/toggle-featured', [CategoryController::class, 'toggleFeatured'])->name('categories.toggleFeatured');

        // Comments Management
        Route::get('/comments', [CommentController::class, 'index'])->name('comments.index');
        Route::patch('/comments/{comment}/approve', [CommentController::class, 'approve'])->name('comments.approve');
        Route::delete('/comments/{comment}', [CommentController::class, 'destroy'])->name('comments.destroy');

        // User Management
        Route::post('/users/{user}/toggle-admin', [AdminUserController::class, 'toggleAdmin'])
            ->name('users.toggle-admin');
    }); 