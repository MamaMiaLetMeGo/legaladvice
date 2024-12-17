<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use OpenAI\Exceptions\ErrorException;
use Illuminate\Support\Facades\Cache;

class OpenAIService
{
    public function generateResponse(string $message, array $context = []): ?string
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

            // Add conversation context if any
            foreach ($context as $msg) {
                if (!isset($msg['role']) || !isset($msg['content'])) {
                    Log::warning('Invalid context message format', ['message' => $msg]);
                    continue;
                }
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

            Log::info('Sending request to OpenAI', [
                'message' => $message,
                'context_count' => count($context)
            ]);

            // Set rate limiting
            Cache::put($key, true, now()->addSeconds(2));

            try {
                $result = OpenAI::chat()->create([
                    'model' => 'gpt-3.5-turbo',
                    'messages' => $messages,
                    'max_tokens' => 500,
                    'temperature' => 0.7,
                ]);

                if (!isset($result->choices[0]->message->content)) {
                    Log::error('Invalid response structure from OpenAI', [
                        'result' => $result
                    ]);
                    throw new \Exception('Invalid response from OpenAI');
                }

                return $result->choices[0]->message->content;

            } catch (ErrorException $e) {
                Log::error('OpenAI API Error', [
                    'message' => $e->getMessage(),
                    'code' => $e->getCode(),
                    'trace' => $e->getTraceAsString()
                ]);

                if (str_contains($e->getMessage(), 'Rate limit')) {
                    return 'I apologize, but our service is experiencing high demand. Please try again in a moment.';
                }

                if (str_contains($e->getMessage(), 'Invalid authentication')) {
                    throw new \Exception('OpenAI authentication error. Please check your API key.');
                }

                throw new \Exception('Error communicating with AI service: ' . $e->getMessage());
            }

        } catch (\Exception $e) {
            Log::error('OpenAI Service Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            // Re-throw authentication errors
            if (str_contains($e->getMessage(), 'API key')) {
                throw $e;
            }

            // For other errors, return a user-friendly message
            return 'I apologize, but I am currently unable to process your request. Please try again in a moment.';
        }
    }
}
