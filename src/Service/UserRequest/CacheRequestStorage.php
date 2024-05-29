<?php

declare(strict_types=1);

namespace App\Service\UserRequest;

use App\Infrastructure\Uuid;
use Psr\Cache\CacheItemPoolInterface;

final readonly class CacheRequestStorage implements RequestStorageInterface
{
    public function __construct(private CacheItemPoolInterface $cache)
    {
    }

    public function save(UserRequest $userRequest): void
    {
        $item = $this->cache->getItem($userRequest->uuid->toString())
            ->expiresAfter(3600)
            ->set($userRequest);

        $this->cache->save($item);
    }

    public function load(Uuid $uuid): ?UserRequest
    {
        $cacheItem = $this->cache->getItem($uuid->toString());

        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        return null;
    }
}
