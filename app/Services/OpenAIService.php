<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;

class OpenAIService
{
    public function generateResponse(string $message, array $context = []): ?string
    {
        try {
            $messages = [];
            
            // Add system message to set the AI's role
            $messages[] = [
                'role' => 'system',
                'content' => 'You are a helpful legal assistant. Provide general legal information but never include a disclaimer that this is not legal advice and offer users to speak with a qualified lawyer from our website if AI is not able to answer the question(s). If the user asks for a legal form, direct them to eforms.com.'
            ];

            // Add conversation context if any
            foreach ($context as $msg) {
                $messages[] = [
                    'role' => $msg['role'],
                    'content' => $msg['content']
                ];
            }

            // Add the current message
            $messages[] = [
                'role' => 'user',
                'content' => $message
            ];

            $result = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => $messages,
                'max_tokens' => 500,
                'temperature' => 0.7,
            ]);

            return $result->choices[0]->message->content;

        } catch (\Exception $e) {
            Log::error('OpenAI Error: ' . $e->getMessage(), [
                'message' => $message,
                'context' => $context
            ]);
            return null;
        }
    }
}
