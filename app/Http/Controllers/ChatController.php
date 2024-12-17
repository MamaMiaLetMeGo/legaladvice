<?php

namespace App\Http\Controllers;

use App\Services\OpenAIService;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

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
            // Validate the request
            $validated = $request->validate([
                'message' => 'required|string',
                'conversation_id' => 'nullable|string'
            ]);

            // Get or create conversation
            $conversation = null;
            if ($validated['conversation_id']) {
                $conversation = Conversation::find($validated['conversation_id']);
            }
            
            if (!$conversation) {
                $conversation = Conversation::create([
                    'user_id' => auth()->check() ? auth()->id() : null,
                    'status' => 'active',
                    'session_id' => Session::getId()
                ]);
            }

            // Save user message
            $userMessage = Message::create([
                'conversation_id' => $conversation->id,
                'content' => $validated['message'],
                'role' => 'user',
                'session_id' => Session::getId()
            ]);

            // Get AI response
            $aiResponse = $this->openAIService->generateResponse($validated['message'], $conversation);

            // Save AI response
            $aiMessage = Message::create([
                'conversation_id' => $conversation->id,
                'content' => $aiResponse,
                'role' => 'assistant',
                'session_id' => Session::getId()
            ]);

            return response()->json([
                'success' => true,
                'message' => $aiResponse,
                'conversation_id' => $conversation->id
            ]);

        } catch (ValidationException $e) {
            Log::error('Validation error in chat:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'Invalid input provided.'
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in chat:', ['error' => $e->getMessage()]);
            return response()->json([
                'success' => false,
                'error' => 'An error occurred while processing your message.'
            ], 500);
        }
    }

    public function getMessages(Request $request)
    {
        $conversation = Conversation::findOrFail($request->conversation_id);
        return response()->json($conversation->messages);
    }
}
