<?php

declare(strict_types=1);

namespace App\MessageBus\Message;

use App\Infrastructure\Uuid;
use App\Service\Review;

final readonly class AnalyzeCommand
{
    public function __construct(
        public Uuid $uuid,
        /** @var array<Review> */
        public array $reviews,
        public string $analyzer
    ) {
    }
}
