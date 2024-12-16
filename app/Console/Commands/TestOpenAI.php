<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use OpenAI\Laravel\Facades\OpenAI;
use Illuminate\Support\Facades\Cache;

class TestOpenAI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'openai:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test OpenAI integration with minimal tokens';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            // Check rate limiting
            $key = 'openai_test_limit';
            if (Cache::has($key)) {
                $this->error('Please wait before testing again (rate limiting)');
                return;
            }

            // Set a 1-minute cooldown
            Cache::put($key, true, now()->addMinute());

            $result = OpenAI::chat()->create([
                'model' => 'gpt-3.5-turbo',
                'max_tokens' => 10, // Limit token usage
                'messages' => [
                    ['role' => 'user', 'content' => 'Hi'],
                ],
                'temperature' => 0.5, // More focused responses
            ]);

            $this->info('Response: ' . $result->choices[0]->message->content);
            $this->info('Test successful with minimal tokens!');
            
            // Show token usage
            $this->info('Tokens used: ' . ($result->usage->total_tokens ?? 'unknown'));
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            
            if (str_contains($e->getMessage(), 'quota')) {
                $this->warn('Please check your OpenAI billing at: https://platform.openai.com/account/billing');
            }
        }
    }
}
