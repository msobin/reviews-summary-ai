<?php

declare(strict_types=1);

namespace App\Service\ReviewParser;

use App\Service\Review;

interface ReviewParserInterface
{
    /**
     * @return array<Review>
     */
    public function parse(string $url): array;

    public function supports(string $url): bool;
}
