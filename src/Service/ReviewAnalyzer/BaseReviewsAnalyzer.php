<?php

declare(strict_types=1);

namespace App\Service\ReviewAnalyzer;

use App\Service\ConfigService;
use App\Service\Review;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.reviews_analyzer')]
abstract class BaseReviewsAnalyzer
{
    public function __construct(private readonly ConfigService $configService)
    {
    }

    abstract public static function getName(): string;

    /** @param array<Review> $reviews*/
    abstract public function analyze(array $reviews): string;

    protected function getConfigKey(string $key): mixed
    {
        return $this->configService->get(self::getName(), $key);
    }
}
