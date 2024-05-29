<?php

declare(strict_types=1);

namespace App\Service\ReviewAnalyzer;

use App\Service\Review;

interface ReviewAnalyzerInterface
{
    /**
     * @param array<Review> $reviews
     */
    public function analyze(array $reviews): string;
}
