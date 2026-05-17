<?php

declare(strict_types=1);

namespace CommonPHP\Cache\Tests\Fixtures;

use CommonPHP\Cache\Contracts\CacheSerializerInterface;
use Throwable;

final class FailingCacheSerializer implements CacheSerializerInterface
{
    public function __construct(
        private Throwable $throwable,
    ) {
    }

    public function serialize(mixed $value): string
    {
        throw $this->throwable;
    }

    public function unserialize(string $payload): mixed
    {
        throw $this->throwable;
    }
}
