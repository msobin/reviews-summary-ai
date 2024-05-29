<?php

declare(strict_types=1);

namespace App\Service;

use App\Infrastructure\Uuid;
use App\Service\UserRequest\UserRequest;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use Longman\TelegramBot\Telegram;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[Autoconfigure(lazy: true)]
class BotService
{
    private Telegram $telegram;

    /**
     * @throws TelegramException
     */
    public function __construct(
        #[Autowire(env: 'BOT_API_KEY')] string $botApiKey,
    ) {
        $this->telegram = (new Telegram($botApiKey, 'review_bot'))->useGetUpdatesWithoutDatabase();

        Request::initialize($this->telegram);
    }

    public function getBot(): Telegram
    {
        return $this->telegram;
    }

    /**
     * @return array<array-key, UserRequest>
     * @throws TelegramException
     */
    public function getRequests(): array
    {
        $response = $this->telegram->handleGetUpdates();
        $messages = [];

        if (!$response->isOk()) {
            return [];
        }

        $updates = $response->getResult();
        foreach ($updates as $update) {
            $messages[] = new UserRequest(
                $update->getMessage()->getFrom()->getId(),
                $update->getMessage()->getText(),
                Uuid::v7()
            );
        }

        return $messages;
    }

    /**
     * @throws TelegramException
     */
    public function sendMessage(int $chatId, string $message): void
    {
        Request::sendMessage([
            'chat_id' => $chatId,
            'text' => $message,
        ]);
    }
}
