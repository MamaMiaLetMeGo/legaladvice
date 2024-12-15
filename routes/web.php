<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryViewController;
use App\Models\Post;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthorController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\WelcomeBackController;
use App\Http\Controllers\TwoFactorAuthController;
use App\Http\Controllers\Auth\TwoFactorChallengeController;
use App\Http\Controllers\SecurityController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Lawyer\LawyerDashboardController;
use App\Models\Conversation;
use App\Http\Middleware\IsLawyer;
use App\Http\Middleware\IsAdmin;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;

Route::middleware('web')->group(function () {
    // Include auth routes first
    require __DIR__.'/auth.php';

    // Add this dashboard route before other routes
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    });

    // Static routes (no parameters)
    Route::get('/', function () {
        $posts = Post::with(['author', 'categories'])
            ->published()
            ->latest('published_date')
            ->take(6)
            ->get();
        return view('home', compact('posts'));
    })->name('home');

    Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
    Route::post('/contact', [ContactController::class, 'submit'])->name('contact.submit');
    Route::get('/categories', [CategoryViewController::class, 'index'])->name('categories.index');
    Route::get('/legal-expert', function () {
        return view('legal-expert');
    })->name('legal-expert');
    Route::get('/pricing', function () {
        return view('pricing');
    })->name('pricing');

    // Lawyer routes (protected, specific prefix)
    Route::middleware(['auth', IsLawyer::class])->prefix('lawyer')->name('lawyer.')->group(function () {
        Route::get('/dashboard', [LawyerDashboardController::class, 'index'])->name('dashboard');
        Route::get('/stats', [LawyerDashboardController::class, 'getStats'])->name('stats');
        Route::post('/conversations/bulk-delete', [LawyerDashboardController::class, 'bulkDelete'])->name('conversations.bulk-delete');
        Route::post('/conversations/bulk-close', [LawyerDashboardController::class, 'bulkClose'])->name('conversations.bulk-close');
        Route::delete('/conversations/{conversation}', [LawyerDashboardController::class, 'destroy'])->name('conversations.destroy');

        // Chat routes
        Route::get('/pending-conversations', [ChatController::class, 'getPendingConversations'])->name('pending-conversations');
        Route::get('/active-conversations', [ChatController::class, 'getActiveConversations'])->name('active-conversations');
        Route::post('/claim-conversation/{conversation}', [ChatController::class, 'claimConversation'])->name('claim-conversation');
        Route::get('/conversation/{conversation}', [ChatController::class, 'showConversation'])->name('conversation.show');
        Route::post('/send-message', [ChatController::class, 'lawyerSendMessage'])->name('send-message');
        Route::get('/conversation/{conversation}/messages', [ChatController::class, 'getMessages'])->name('conversation.messages');
    });

    // Authentication required routes
    Route::middleware(['auth'])->group(function () {
        // 2FA routes (without verification)
        Route::prefix('2fa')->name('2fa.')->group(function () {
            Route::get('/', [TwoFactorChallengeController::class, 'create'])->name('challenge');
            Route::post('/', [TwoFactorChallengeController::class, 'store'])->name('verify');
            Route::get('/recovery', [TwoFactorChallengeController::class, 'showRecoveryForm'])->name('recovery');
            Route::post('/recovery', [TwoFactorChallengeController::class, 'recovery'])->name('recovery.store');
        });

        Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    });

    // Routes requiring 2FA
    Route::middleware(['auth', 'two-factor'])->group(function () {
        Route::get('/welcome', [WelcomeController::class, 'newUser'])->name('welcome.new-user');
        Route::get('/welcome-back', [WelcomeBackController::class, 'index'])->name('welcome.back');
        
        // Profile routes
        Route::prefix('profile')->name('profile.')->group(function () {
            Route::get('/', [ProfileController::class, 'show'])->name('show');
            Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
            Route::patch('/', [ProfileController::class, 'update'])->name('update');
            Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
            Route::get('/security', [SecurityController::class, 'show'])->name('security');

            // 2FA settings
            Route::prefix('2fa')->name('2fa.')->group(function () {
                Route::get('/', [TwoFactorAuthController::class, 'show'])->name('show');
                Route::post('/enable', [TwoFactorAuthController::class, 'enable'])->name('enable');
                Route::post('/disable', [TwoFactorAuthController::class, 'disable'])->name('disable');
                Route::get('/recovery-codes', [TwoFactorAuthController::class, 'showRecoveryCodes'])->name('recovery-codes');
                Route::post('/recovery-codes', [TwoFactorAuthController::class, 'regenerateRecoveryCodes'])->name('recovery-codes.regenerate');
            });
        });

        // Newsletter routes
        Route::prefix('newsletter')->name('newsletter.')->group(function () {
            Route::post('/subscribe', [NewsletterController::class, 'subscribe'])->name('subscribe');
            Route::post('/unsubscribe', [NewsletterController::class, 'unsubscribe'])->name('unsubscribe');
        });

        // Authors routes
        Route::get('/authors', [AuthorController::class, 'index'])->name('authors.index');
        Route::get('/authors/{user}', [AuthorController::class, 'show'])->name('authors.show');
    });

    // Chat API routes (public)
    Route::post('/api/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::get('/api/chat/conversation', [ChatController::class, 'getConversation']);

    // Catch-all routes (must be last)
    Route::get('/{category:slug}/{post:slug}', [PostController::class, 'show'])->name('posts.show');
    Route::get('/{category:slug}', [CategoryViewController::class, 'show'])->name('categories.show');
});

// Development only routes
if (app()->environment('local')) {
    Route::get('/test-ip', function () {
        dd([
            'ip()' => request()->ip(),
            'getClientIp' => request()->getClientIp(),
            'server.REMOTE_ADDR' => request()->server('REMOTE_ADDR'),
            'headers' => request()->headers->all(),
        ]);
    });
}