<?php

namespace App\Services;

use OpenAI;

class OpenAIService
{
    protected $client;

    public function __construct()
    {
        $this->client = OpenAI::client(config('services.openai.key'));
    }

    public function generateChirps(): array
    {
        $response = $this->client->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a creative assistant that generates chirps for a social media platform.'],
                ['role' => 'user', 'content' => 'Generate 3 chirps about daily life with varying levels of seriousness.'],
            ],
            'max_tokens' => 100,
        ]);
    
        // Extrahera text från svaret
        $chirps = explode("\n", $response['choices'][0]['message']['content']);
    
        // Filtrera bort tomma rader och trimma whitespace
        return array_filter(array_map('trim', $chirps));
    }
    
}
