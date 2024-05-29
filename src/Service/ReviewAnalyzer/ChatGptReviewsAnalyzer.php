<?php

declare(strict_types=1);

namespace App\Service\ReviewAnalyzer;

use App\Service\ConfigService;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class ChatGptReviewsAnalyzer extends BaseReviewsAnalyzer
{
    private Client $client;

    public function __construct(private readonly ConfigService $configService)
    {
        parent::__construct($configService);

        $this->client = new Client([
            'base_uri' => 'https://api.openai.com',
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->getConfigKey('api_key'),
            ]
        ]);
    }

    public static function getName(): string
    {
        return 'chat_gpt';
    }

    /**
     * @throws GuzzleException
     */
    public function analyze(array $reviews): string
    {
        $reviews = array_slice($reviews, 0, 200);

        if (!$reviews) {
            return 'No reviews to analyze';
        }

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
                        'content' => $this->getConfigKey('system_prompt'),
                    ],
                    [
                        'role' => 'user',
                        'content' => $this->getConfigKey('user_prompt') . $reviewContent,
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
