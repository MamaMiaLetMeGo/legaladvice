<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Api\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public API routes
Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('api.chat.send');
Route::get('/chat/conversation', [ChatController::class, 'getConversation'])->name('api.chat.conversation');

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    // User info
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Protected chat routes
    Route::prefix('chat')->name('api.chat.')->group(function () {
        Route::get('/messages', [ChatController::class, 'getMessages'])->name('messages');
        Route::post('/mark-read', [ChatController::class, 'markAsRead'])->name('mark-read');
    });
});

// Note: Don't use 'web' middleware in API routes as it includes session handling
// which isn't typically needed for APIs. Sanctum handles authentication differently.