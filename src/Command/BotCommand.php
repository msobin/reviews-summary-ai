<?php

declare(strict_types=1);

namespace App\Command;

use App\MessageBus\Message\ParseCommand;
use App\Service\BotService;
use App\Service\UserRequest\RequestStorageInterface;
use Longman\TelegramBot\Exception\TelegramException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsCommand('app:bot')]
final class BotCommand extends Command
{
    public function __construct(
        private readonly BotService $botService,
        private readonly MessageBusInterface $messageBus,
        private readonly RequestStorageInterface $requestStorage
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        while (true) {
            $this->processMessages();

            sleep(1);
        }
    }

    /**
     * @throws TelegramException
     */
    private function processMessages(): void
    {
        $requests = $this->botService->getRequests();

        foreach ($requests as $request) {
            $this->requestStorage->save($request);

            $this->messageBus->dispatch(new ParseCommand($request->uuid, $request->url));
        }
    }
}
