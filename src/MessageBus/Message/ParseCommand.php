<?php

declare(strict_types=1);

namespace App\MessageBus\Message;

use App\Infrastructure\Uuid;

final readonly class ParseCommand
{
    public function __construct(
        public Uuid $uuid,
        public string $url
    ) {
    }
}
