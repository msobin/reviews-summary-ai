<?php

declare(strict_types=1);

namespace App\Service\UserRequest;

use App\Infrastructure\Uuid;

final readonly class UserRequest
{
    public function __construct(
        public readonly int $chatId,
        public readonly string $url,
        public readonly Uuid $uuid
    ) {
    }
}
