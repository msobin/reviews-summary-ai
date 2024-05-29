<?php

declare(strict_types=1);

namespace App\Service\ReviewAnalyzer;

use App\Service\ConfigService;
use GuzzleHttp\Client;

class YandexGptReviewsAnalyzer extends BaseReviewsAnalyzer
{
    private Client $client;

    public static function getName(): string
    {
        return 'yandex_gpt';
    }

    public function __construct(private readonly ConfigService $configService)
    {
        parent::__construct($configService);

        $this->client = new Client([
            'base_uri' => 'https://llm.api.cloud.yandex.net',
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . '',
            ]
        ]);
    }

    public function analyze(array $reviews): string
    {
//        $response = $this->client->post('/foundationModels/v1/completion', [
//            'json' => [
//                'modelUri' => 'string',
//                'completionOptions' => [
//                    'stream' => true,
//                    'temperature' => 'number',
//                    'maxTokens' => 'integer'
//                ],
//                'messages' => [
//                    [
//                        'role' => 'system',
//                        'content' => $this->systemPrompt,
//                    ],
//                    [
//                        'role' => 'user',
//                        'content' => $this->userPrompt . $reviewContent,
//                    ],
//                ],
//            ],
//        ]);

        return 'Not implemented yet';
    }
}
