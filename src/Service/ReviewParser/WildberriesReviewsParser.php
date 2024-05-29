<?php

declare(strict_types=1);

namespace App\Service\ReviewParser;

use App\Service\Review;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

final readonly class WildberriesReviewsParser implements ReviewParserInterface
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public static function isSupports(string $url): bool
    {
        $parsedUrl = parse_url($url);

        if (!isset($parsedUrl['host']) || !isset($parsedUrl['query'])) {
            return false;
        }

        parse_str($parsedUrl['query'], $queryParams);

        return isset($queryParams['card']) && str_contains($parsedUrl['host'], 'wildberries.ru');
    }

    /**
     * @throws GuzzleException
     */
    public function parse(string $url): array
    {
        $reviews = [];

        $parsedUrl = parse_url($url);
        parse_str($parsedUrl['query'], $queryParams);

        $cardId = $queryParams['card'];

        $response = $this->client->get('https://card.wb.ru/cards/v1/detail?dest=-1257786&nm=' . $cardId);
        $content = $response->getBody()->getContents();

        if ($response->getStatusCode() !== 200 || !json_validate($content)) {
            throw new \RuntimeException('Failed to fetch card');
        }

        $rootId = array_get(json_decode($content, true), 'data.products.0.root');

        $response = $this->client->get('https://feedbacks2.wb.ru/feedbacks/v1/' . $rootId);
        $content = $response->getBody()->getContents();

        if ($response->getStatusCode() !== 200 || !json_validate($content)) {
            throw new \RuntimeException('Failed to fetch reviews');
        }

        if (!$feedbacks = array_get(json_decode($content, true), 'feedbacks')) {
            return [];
        }

        foreach ($feedbacks as $feedback) {
            $reviews[] = new Review($feedback['text'], $feedback['productValuation']);
        }

        return $reviews;
    }
}
