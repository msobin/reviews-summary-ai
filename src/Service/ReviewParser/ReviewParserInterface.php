<?php

declare(strict_types=1);

namespace App\Service\ReviewParser;

use App\Service\Review;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.reviews_parser')]
interface ReviewParserInterface
{
    public static function isSupports(string $url): bool;

    /** @return array<Review> */
    public function parse(string $url): array;
}
