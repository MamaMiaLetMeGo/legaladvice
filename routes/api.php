<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

// Keep your existing sanctum route
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Chat routes with middleware
Route::prefix('chat')
    ->middleware(['web']) // This ensures session state and CSRF protection
    ->group(function () {
        Route::post('/send', [ChatController::class, 'send'])
            ->name('chat.send'); // Adding a name makes it easier to reference the route
            
        Route::get('/conversation', [ChatController::class, 'getConversation'])
            ->name('chat.conversation');
});