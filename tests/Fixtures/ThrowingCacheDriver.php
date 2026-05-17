<?php

declare(strict_types=1);

namespace CommonPHP\Cache\Tests\Fixtures;

use CommonPHP\Cache\CacheItem;
use CommonPHP\Cache\CacheKey;
use CommonPHP\Cache\Contracts\AbstractCacheDriver;
use Throwable;

final class ThrowingCacheDriver extends AbstractCacheDriver
{
    public function __construct(
        private Throwable $throwable,
    ) {
    }

    public function fetch(CacheKey|string $key): ?CacheItem
    {
        $this->fail();
    }

    public function store(CacheItem $item): void
    {
        $this->fail();
    }

    public function delete(CacheKey|string $key): void
    {
        $this->fail();
    }

    public function clear(): void
    {
        $this->fail();
    }

    public function has(CacheKey|string $key): bool
    {
        $this->fail();
    }

    private function fail(): never
    {
        throw $this->throwable;
    }
}
