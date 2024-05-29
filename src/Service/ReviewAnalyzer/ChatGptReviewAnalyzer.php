<?php

declare(strict_types=1);

namespace App\Service\ReviewAnalyzer;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class ChatGptReviewAnalyzer implements ReviewAnalyzerInterface
{
    private Client $client;

    public function __construct(
        #[Autowire(env: 'OPENAI_API_KEY')] string $apiKey,
        #[Autowire(env: 'SYSTEM_PROMPT')] private string $systemPrompt,
        #[Autowire(env: 'USER_PROMPT')] private string $userPrompt,
    ) {
        $this->client = new Client([
            'base_uri' => 'https://api.openai.com',
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiKey,
            ]
        ]);
    }

    /**
     * @throws GuzzleException
     */
    public function analyze(array $reviews): string
    {
        $reviews = array_slice($reviews, 0, 100);       // @todo debug

        $reviewContent = '';
        foreach ($reviews as $review) {
            $reviewContent .= sprintf(
                "\n------\nfeedback: %s\nrating: %1.1f",
                $review->text,
                $review->rating
            );
        }

        $response = $this->client->post('/v1/chat/completions', [
            'json' => [
                'model' => 'gpt-3.5-turbo',
                'messages' => [
                    [
                        'role' => 'system',
                        'content' => $this->systemPrompt,
                    ],
                    [
                        'role' => 'user',
                        'content' => $this->userPrompt . $reviewContent,
                    ],
                ],
            ],
        ]);

        if ($response->getStatusCode() !== 200) {
            return 'Failed to analyze reviews';
        }

        $content = json_decode($response->getBody()->getContents(), true);

        return strval(array_get($content, 'choices.0.message.content', ''));
    }
}
