<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use OpenAI\Exceptions\ErrorException;
use Illuminate\Support\Facades\Cache;

class OpenAIService
{
    public function generateResponse(string $message, $conversation = null): ?string
    {
        if (empty($message)) {
            Log::warning('Empty message provided to OpenAI service');
            return 'I apologize, but I did not receive any message to respond to. Please try again.';
        }

        // Check API key
        if (!config('openai.api_key')) {
            Log::error('OpenAI API key not configured');
            throw new \Exception('OpenAI API key not configured');
        }

        try {
            // Check rate limiting
            $key = 'openai_request_' . md5($message);
            if (Cache::has($key)) {
                Log::warning('Rate limit hit for message', ['message' => $message]);
                return 'I apologize, but you are sending messages too quickly. Please wait a moment before trying again.';
            }

            $messages = [];
            
            // Add system message to set the AI's role
            $messages[] = [
                'role' => 'system',
                'content' => 'You are a helpful legal assistant. Provide general legal information but never include a disclaimer that this is not legal advice and offer users to speak with a qualified lawyer from our website if AI is not able to answer the question(s). If the user asks for a legal form, direct them to eforms.com.'
            ];

            // Add conversation context if available
            if ($conversation) {
                $contextMessages = $conversation->messages()
                    ->orderBy('created_at', 'desc')
                    ->take(5)
                    ->get()
                    ->map(function ($msg) {
                        return [
                            'role' => $msg->role,
                            'content' => $msg->content
                        ];
                    })
                    ->reverse()
                    ->values()
                    ->all();

                $messages = array_merge($messages, $contextMessages);
            }

            // Add the current message
            $messages[] = [
                'role' => 'user',
                'content' => $message
            ];

            // Call OpenAI API
            $response = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => $messages,
                'max_tokens' => 1000,
                'temperature' => 0.7,
                'frequency_penalty' => 0.0,
                'presence_penalty' => 0.6,
            ]);

            // Set rate limiting
            Cache::put($key, true, now()->addSeconds(3));

            // Extract and return the response
            $aiMessage = $response->choices[0]->message->content;
            
            if (empty($aiMessage)) {
                Log::error('Empty response from OpenAI', [
                    'response' => $response
                ]);
                return 'I apologize, but I encountered an error generating a response. Please try again.';
            }

            return $aiMessage;

        } catch (ErrorException $e) {
            Log::error('OpenAI API error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            if (str_contains($e->getMessage(), 'Rate limit')) {
                return 'I apologize, but our service is experiencing high demand. Please try again in a moment.';
            }

            throw $e;
        }
    }
}
