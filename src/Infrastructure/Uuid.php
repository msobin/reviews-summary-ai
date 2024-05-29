<?php

declare(strict_types=1);

namespace App\Infrastructure;

use Ramsey\Uuid\Uuid as RamseyUuid;

use function Ramsey\Uuid\v7;

final readonly class Uuid implements \JsonSerializable
{
    private function __construct(
        private string $uuid,
    ) {
    }

    public static function v7(\DateTimeImmutable $time = new \DateTimeImmutable()): self
    {
        return new self(v7($time));
    }

    public static function fromString(string $uuid): self
    {
        if (RamseyUuid::isValid($uuid)) {
            return new self($uuid);
        }

        throw new \InvalidArgumentException(sprintf('"%s" is not a valid UUID.', $uuid));
    }

    public function __toString(): string
    {
        return $this->uuid;
    }

    public function toString(): string
    {
        return $this->uuid;
    }

    public function jsonSerialize(): string
    {
        return $this->uuid;
    }
}
