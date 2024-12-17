<?php

namespace App\Http\Controllers;

use App\Services\OpenAIService;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;

class ChatController extends Controller
{
    protected $openAIService;

    public function __construct(OpenAIService $openAIService)
    {
        $this->openAIService = $openAIService;
    }

    public function sendMessage(Request $request)
    {
        try {
            // Log request details for debugging
            Log::info('Message request received', [
                'request_data' => $request->all(),
                'headers' => $request->headers->all(),
                'session_id' => session()->getId(),
                'has_session' => $request->hasSession(),
                'user_agent' => $request->header('User-Agent')
            ]);

            // Ensure session is started
            if (!$request->hasSession()) {
                Log::error('Session not active', [
                    'session_id' => session()->getId()
                ]);
                return response()->json([
                    'success' => false,
                    'error' => 'Session error',
                    'details' => 'Please refresh the page and try again.'
                ], 401);
            }

            // Validate the request
            $validated = $request->validate([
                'message' => 'required|string|max:1000',
                'conversation_id' => 'nullable|exists:conversations,id'
            ]);

            // Get user ID and session ID
            $userId = auth()->id();
            $sessionId = session()->getId();
            $isGuest = !$userId;

            Log::info('User info', [
                'user_id' => $userId,
                'session_id' => $sessionId,
                'is_guest' => $isGuest,
                'ip_address' => $request->ip()
            ]);

            // Get or create conversation
            $conversation = null;
            if ($request->conversation_id) {
                $conversation = Conversation::findOrFail($request->conversation_id);
            } else {
                $conversation = Conversation::create([
                    'user_id' => $userId,
                    'status' => 'active',
                    'session_id' => $sessionId,
                    'is_guest' => $isGuest,
                    'ip_address' => $request->ip()
                ]);
            }

            Log::info('Conversation created/found', ['conversation' => $conversation]);

            // Save user message
            $userMessage = $conversation->messages()->create([
                'content' => $validated['message'],
                'user_id' => $userId,
                'is_from_user' => true,
                'session_id' => $sessionId,
                'is_guest' => $isGuest,
                'ip_address' => $request->ip()
            ]);

            Log::info('User message saved', ['message' => $userMessage]);

            // Get conversation context
            $context = $conversation->messages()
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get()
                ->map(function ($msg) {
                    return [
                        'role' => $msg->is_from_user ? 'user' : 'assistant',
                        'content' => $msg->content
                    ];
                })
                ->reverse()
                ->values()
                ->all();

            // Get AI response
            $aiResponse = $this->openAIService->generateResponse(
                $validated['message'],
                $context
            );

            if (!$aiResponse) {
                throw new \Exception('Failed to generate AI response');
            }

            Log::info('AI response received', ['response' => $aiResponse]);

            // Save AI response
            $aiMessage = $conversation->messages()->create([
                'content' => $aiResponse,
                'is_from_user' => false,
                'session_id' => $sessionId,
                'is_guest' => false,
                'ip_address' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'conversation_id' => $conversation->id,
                'message' => $aiMessage->content
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error', [
                'errors' => $e->errors(),
            ]);
            
            return response()->json([
                'success' => false,
                'error' => 'Invalid input',
                'details' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Error in sendMessage', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            $errorMessage = 'An unexpected error occurred';
            $statusCode = 500;

            if (str_contains($e->getMessage(), 'API key')) {
                $errorMessage = 'AI service configuration error. Please contact support.';
                $statusCode = 503;
            } elseif (str_contains($e->getMessage(), 'Rate limit')) {
                $errorMessage = 'Too many requests. Please wait a moment before trying again.';
                $statusCode = 429;
            }

            return response()->json([
                'success' => false,
                'error' => $errorMessage,
                'details' => app()->environment('local') ? $e->getMessage() : $errorMessage
            ], $statusCode);
        }
    }

    public function getMessages(Request $request)
    {
        $conversation = Conversation::findOrFail($request->conversation_id);
        return response()->json($conversation->messages);
    }
}
