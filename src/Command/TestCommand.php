<?php

declare(strict_types=1);

namespace App\Command;

use App\Infrastructure\Uuid;
use App\MessageBus\Message\ParseCommand;
use App\Service\BotService;
use App\Service\UserRequest\RequestStorageInterface;
use App\Service\UserRequest\UserRequest;
use Longman\TelegramBot\Exception\TelegramException;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Psr\SimpleCache\CacheInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Cache\TagAwareCacheInterface;

#[AsCommand('app:test')]
final class TestCommand extends Command
{
    public function __construct(private RequestStorageInterface $storage)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $uuid = Uuid::v7();

        $this->storage->save(new UserRequest(1, 'https://example.com',$uuid));
        $test = $this->storage->load($uuid);
        
        return Command::SUCCESS;
    }
}
