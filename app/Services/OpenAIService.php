<?php

namespace App\Services;

use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Log;
use OpenAI\Exceptions\ErrorException;

class OpenAIService
{
    public function generateResponse(string $message, array $context = []): ?string
    {
        if (!config('openai.api_key')) {
            Log::error('OpenAI API key not configured');
            throw new \Exception('OpenAI API key not configured');
        }

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

            Log::info('Sending request to OpenAI', [
                'message' => $message,
                'context_count' => count($context)
            ]);

            $result = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'messages' => $messages,
                'max_tokens' => 500,
                'temperature' => 0.7,
            ]);

            if (!isset($result->choices[0]->message->content)) {
                throw new \Exception('Invalid response from OpenAI');
            }

            return $result->choices[0]->message->content;

        } catch (ErrorException $e) {
            Log::error('OpenAI API Error', [
                'message' => $e->getMessage(),
                'code' => $e->getCode()
            ]);
            throw new \Exception('Error communicating with AI service: ' . $e->getMessage());
        } catch (\Exception $e) {
            Log::error('OpenAI Service Error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }
}
