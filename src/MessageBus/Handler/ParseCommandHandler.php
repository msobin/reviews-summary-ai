<?php

declare(strict_types=1);

namespace App\MessageBus\Handler;

use App\MessageBus\Message\AnalyzeCommand;
use App\MessageBus\Message\ParseCommand;
use App\Service\ReviewAnalyzer\ChatGptReviewsAnalyzer;
use App\Service\ReviewParser\ParsersFactory;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

#[AsMessageHandler]
readonly final class ParseCommandHandler
{
    public function __construct(
        private MessageBusInterface $messageBus,
        private ParsersFactory $parsersFactory,
        private CacheInterface $cache
    ) {
    }

    public function __invoke(ParseCommand $parseMessage): void
    {
        $parser = $this->parsersFactory->getParser($parseMessage->url);

        $cacheKey = md5($parser::class . $parseMessage->url);

        $reviews = $this->cache->get($cacheKey, function (ItemInterface $item) use ($parseMessage, $parser) {
            $item->expiresAfter(3600);

            return $parser->parse($parseMessage->url);
        });

        $this->messageBus->dispatch(new AnalyzeCommand($parseMessage->uuid, $reviews, ChatGptReviewsAnalyzer::getName()));
    }
}
