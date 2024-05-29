<?php

declare(strict_types=1);

namespace App\MessageBus\Handler;

use App\MessageBus\Message\AnalyzerResult;
use App\Service\BotService;
use App\Service\UserRequest\RequestStorageInterface;
use App\Service\UserRequest\UserRequest;
use Longman\TelegramBot\Exception\TelegramException;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
final readonly class AnalyzerResultHandler
{
    public function __construct(
        private RequestStorageInterface $requestStorage,
        private BotService $botService
    ) {
    }

    /**
     * @throws TelegramException
     */
    public function __invoke(AnalyzerResult $analyzerResult): void
    {
        /** @var UserRequest $userRequest */
        if (!$userRequest = $this->requestStorage->load($analyzerResult->uuid)) {
            return;
        }

        $this->botService->sendMessage(
            $userRequest->chatId,
            $userRequest->url . PHP_EOL . PHP_EOL . $analyzerResult->result
        );
    }
}
