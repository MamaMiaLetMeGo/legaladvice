<?php

namespace App\Http\Controllers;

use App\Services\OpenAIService;
use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Log;

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
            Log::info('Message received', [
                'request' => $request->all(),
                'headers' => $request->headers->all()
            ]);

            // Validate the request
            $validated = $request->validate([
                'message' => 'required|string|max:1000',
                'conversation_id' => 'nullable|exists:conversations,id'
            ]);

            Log::info('Validation passed', ['validated' => $validated]);

            // Get or create conversation
            $conversation = $request->conversation_id 
                ? Conversation::findOrFail($request->conversation_id)
                : Conversation::create([
                    'user_id' => auth()->id(),
                    'status' => 'active'
                ]);

            Log::info('Conversation created/found', ['conversation' => $conversation]);

            // Save user message
            $userMessage = $conversation->messages()->create([
                'content' => $validated['message'],
                'user_id' => auth()->id(),
                'is_from_user' => true
            ]);

            Log::info('User message saved', ['message' => $userMessage]);

            // Get AI response
            $aiResponse = $this->openAIService->generateResponse(
                $validated['message']
            );

            Log::info('AI response received', ['response' => $aiResponse]);

            if ($aiResponse) {
                // Save AI response
                $aiMessage = $conversation->messages()->create([
                    'content' => $aiResponse,
                    'is_from_user' => false
                ]);

                return response()->json([
                    'success' => true,
                    'conversation_id' => $conversation->id,
                    'message' => $aiMessage->content
                ]);
            }

            return response()->json([
                'error' => 'Failed to generate response'
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error in sendMessage', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'error' => 'Failed to send message',
                'details' => app()->environment('local') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function getMessages(Request $request)
    {
        $conversation = Conversation::findOrFail($request->conversation_id);
        return response()->json($conversation->messages);
    }
}
