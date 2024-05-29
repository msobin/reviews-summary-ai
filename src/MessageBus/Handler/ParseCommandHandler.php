<?php

declare(strict_types=1);

namespace App\MessageBus\Handler;

use App\MessageBus\Message\AnalyzeCommand;
use App\MessageBus\Message\AnalyzerResult;
use App\MessageBus\Message\ParseCommand;
use App\Service\ReviewParser\ParserCollection;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
readonly final class ParseCommandHandler
{
    public function __construct(private MessageBusInterface $messageBus, private ParserCollection $parserCollection)
    {
    }

    public function __invoke(ParseCommand $parseMessage): void
    {
        try {
            $parser = $this->parserCollection->getParser($parseMessage->url);
        } catch (\InvalidArgumentException $e) {
            $this->messageBus->dispatch(new AnalyzerResult($parseMessage->uuid, $e->getMessage()));

            return;
        }

        $review = $parser->parse($parseMessage->url);

        $this->messageBus->dispatch(new AnalyzeCommand($parseMessage->uuid, $review));
    }
}
