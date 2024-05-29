<?php

declare(strict_types=1);

namespace App\Service;

final readonly class Review
{
    public function __construct(
        public string $text,
        public float $rating
    ) {
    }
}
