<?php

declare(strict_types=1);

namespace CommonPHP\Cache\Tests\Fixtures;

use CommonPHP\Cache\CacheItem;
use CommonPHP\Cache\CacheKey;
use CommonPHP\Cache\Contracts\AbstractCacheDriver;

final class ExposedCacheDriver extends AbstractCacheDriver
{
    public ?CacheItem $item = null;

    /**
     * @var list<string>
     */
    public array $deleted = [];

    public bool $cleared = false;

    public function exposeKey(CacheKey|string $key): CacheKey
    {
        return $this->key($key);
    }

    public function exposeIsLive(?CacheItem $item): bool
    {
        return $this->isLive($item);
    }

    public function fetch(CacheKey|string $key): ?CacheItem
    {
        return $this->item;
    }

    public function store(CacheItem $item): void
    {
        $this->item = $item;
    }

    public function delete(CacheKey|string $key): void
    {
        $key = $this->key($key);
        $this->deleted[] = $key->value();

        if ($this->item?->key()->equals($key)) {
            $this->item = null;
        }
    }

    public function clear(): void
    {
        $this->cleared = true;
        $this->item = null;
    }

    public function has(CacheKey|string $key): bool
    {
        return $this->item !== null && $this->item->key()->equals($key);
    }
}
