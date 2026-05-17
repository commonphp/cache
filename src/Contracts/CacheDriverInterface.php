<?php

declare(strict_types=1);

namespace CommonPHP\Cache\Contracts;

use CommonPHP\Cache\CacheItem;
use CommonPHP\Cache\CacheKey;
use CommonPHP\Runtime\Contracts\DriverInterface;

interface CacheDriverInterface extends DriverInterface
{
    public function fetch(CacheKey|string $key): ?CacheItem;

    public function store(CacheItem $item): void;

    public function delete(CacheKey|string $key): void;

    public function clear(): void;

    public function has(CacheKey|string $key): bool;
}
