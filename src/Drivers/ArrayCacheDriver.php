<?php

declare(strict_types=1);

namespace CommonPHP\Cache\Drivers;

use CommonPHP\Cache\CacheItem;
use CommonPHP\Cache\CacheKey;
use CommonPHP\Cache\Contracts\AbstractCacheDriver;

final class ArrayCacheDriver extends AbstractCacheDriver
{
    /**
     * @var array<string, CacheItem>
     */
    private array $items = [];

    public function fetch(CacheKey|string $key): ?CacheItem
    {
        $key = $this->key($key);
        $item = $this->items[$key->value()] ?? null;

        if ($item !== null && $item->isExpired()) {
            unset($this->items[$key->value()]);

            return null;
        }

        return $item;
    }

    public function store(CacheItem $item): void
    {
        if ($item->isExpired()) {
            unset($this->items[$item->keyName()]);

            return;
        }

        $this->items[$item->keyName()] = $item;
    }

    public function delete(CacheKey|string $key): void
    {
        unset($this->items[$this->key($key)->value()]);
    }

    public function clear(): void
    {
        $this->items = [];
    }

    public function has(CacheKey|string $key): bool
    {
        return $this->fetch($key) !== null;
    }

    /**
     * @return array<string, CacheItem>
     */
    public function all(): array
    {
        foreach (array_keys($this->items) as $key) {
            $this->fetch($key);
        }

        return $this->items;
    }
}
