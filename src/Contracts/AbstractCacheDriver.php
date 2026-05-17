<?php

declare(strict_types=1);

namespace CommonPHP\Cache\Contracts;

use CommonPHP\Cache\CacheItem;
use CommonPHP\Cache\CacheKey;

abstract class AbstractCacheDriver implements CacheDriverInterface
{
    public function getName(): string
    {
        return static::class;
    }

    protected function key(CacheKey|string $key): CacheKey
    {
        return CacheKey::from($key);
    }

    protected function isLive(?CacheItem $item): bool
    {
        return $item !== null && !$item->isExpired();
    }
}
