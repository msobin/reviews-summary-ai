<?php

declare(strict_types=1);

namespace App\Service;

use App\Service\ReviewAnalyzer\ChatGptReviewsAnalyzer;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

final class ConfigService
{
    private array $config;

    public function __construct(
        #[Autowire(env: 'OPENAI_API_KEY')] string $apiKey,
        #[Autowire(env: 'SYSTEM_PROMPT')] private string $systemPrompt,
        #[Autowire(env: 'USER_PROMPT')] private string $userPrompt,
    ) {
        $this->config = [
            ChatGptReviewsAnalyzer::getName() => [
                'api_key' => $apiKey,
                'system_prompt' => $this->systemPrompt,
                'user_prompt' => $this->userPrompt,
            ],
        ];
    }

    public function get(string $section, string $key, mixed $default = null): mixed
    {
        return array_get($this->config, $section . '.' . $key, $default);
    }
}
