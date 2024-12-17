<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

// Keep your existing sanctum route
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Chat routes with middleware
Route::middleware(['web', 'session'])->group(function () {
    Route::prefix('chat')->group(function () {
        Route::post('/send', [ChatController::class, 'sendMessage'])
            ->name('api.chat.send');
        Route::get('/conversation', [ChatController::class, 'getConversation'])
            ->name('api.chat.conversation');
    });
});