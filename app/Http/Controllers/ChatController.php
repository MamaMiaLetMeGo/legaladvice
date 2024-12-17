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

            // Start session if not already started
            if (!$request->session()->isStarted()) {
                $request->session()->start();
            }

            // Validate the request
            $validated = $request->validate([
                'message' => 'required|string|max:1000',
                'conversation_id' => 'nullable|string'
            ]);

            // Get or create conversation
            $conversation = null;
            if (!empty($validated['conversation_id'])) {
                $conversation = Conversation::find($validated['conversation_id']);
            }
            
            if (!$conversation) {
                $conversation = Conversation::create([
                    'user_id' => auth()->check() ? auth()->id() : null,
                    'session_id' => session()->getId(),
                    'status' => 'active'
                ]);
            }

            // Create user message
            $userMessage = new Message([
                'content' => $validated['message'],
                'role' => 'user',
                'session_id' => session()->getId()
            ]);
            $conversation->messages()->save($userMessage);

            // Get AI response
            $aiResponse = $this->openAIService->generateResponse($validated['message'], $conversation);

            // Create AI message
            $aiMessage = new Message([
                'content' => $aiResponse,
                'role' => 'assistant',
                'session_id' => session()->getId()
            ]);
            $conversation->messages()->save($aiMessage);

            return response()->json([
                'success' => true,
                'message' => $aiResponse,
                'conversation_id' => $conversation->id
            ]);

        } catch (\Exception $e) {
            Log::error('Error in sendMessage', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'error' => 'An error occurred while processing your message.',
                'debug' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function getMessages(Request $request)
    {
        $conversation = Conversation::findOrFail($request->conversation_id);
        return response()->json($conversation->messages);
    }
}
