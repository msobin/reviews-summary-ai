<?php

declare(strict_types=1);

namespace App\Service\UserRequest;

use App\Infrastructure\Uuid;

interface RequestStorageInterface
{
    public function save(UserRequest $userRequest): void;
    public function load(Uuid $uuid): ?UserRequest;
}
