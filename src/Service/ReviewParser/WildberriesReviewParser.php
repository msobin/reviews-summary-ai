<?php

declare(strict_types=1);

namespace App\Service\ReviewParser;

use App\Service\Review;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DependencyInjection\Attribute\AutoconfigureTag;

#[AutoconfigureTag('app.review_parser')]
final readonly class WildberriesReviewParser implements ReviewParserInterface
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * @throws GuzzleException
     */
    public function parse(string $url): array
    {
        $reviews = [];

        /** @var array<string, string> $parsedUrl */
        $parsedUrl = parse_url($url);

        parse_str($parsedUrl['query'], $queryParams);

        /** @var array<string, string> $queryParams */
        $cardId = $queryParams['card'];

        $response = $this->client->get('https://card.wb.ru/cards/v1/detail?dest=-1257786&nm=' . $cardId);
        $content = $response->getBody()->getContents();

        if ($response->getStatusCode() !== 200 || !json_validate($content)) {
            throw new \RuntimeException('Failed to fetch card');
        }

        /** @var array<string, mixed> $card */
        $card = json_decode($content, true);
        $rootId = array_get($card, 'data.products.0.root');

        $response = $this->client->get('https://feedbacks2.wb.ru/feedbacks/v1/' . $rootId);
        $content = $response->getBody()->getContents();

        if ($response->getStatusCode() !== 200 || !json_validate($content)) {
            throw new \RuntimeException('Failed to fetch reviews');
        }

        /** @var array<string, mixed> $data */
        $data = json_decode($content, true);

        /** @var array<int, array<string, mixed>> $feedbacks */
        $feedbacks = array_get($data, 'feedbacks');

        if (!$feedbacks){
            return [];
        }

        /** @var array{'text': string, 'productValuation': int} $feedback */
        foreach ($feedbacks as $feedback) {
            $reviews[] = new Review($feedback['text'], $feedback['productValuation']);
        }

        return $reviews;
    }

    public function supports(string $url): bool
    {
        /** @var array<string, string> $parsedUrl */
        $parsedUrl = parse_url($url);

        if (!isset($parsedUrl['host']) || !isset($parsedUrl['query'])) {
            return false;
        }

        parse_str($parsedUrl['query'], $queryParams);

        return isset($queryParams['card']) && str_contains($parsedUrl['host'], 'wildberries.ru');
    }
}
