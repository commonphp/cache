<?php

declare(strict_types=1);

namespace CommonPHP\Cache\Contracts;

use CommonPHP\Cache\CacheKey;
use CommonPHP\Cache\CacheTtl;
use DateInterval;
use DateTimeInterface;

interface CacheInterface
{
    public function get(CacheKey|string $key, mixed $default = null): mixed;

    public function set(
        CacheKey|string $key,
        mixed $value,
        CacheTtl|DateInterval|DateTimeInterface|int|null $ttl = null,
    ): static;

    public function delete(CacheKey|string $key): static;

    public function clear(): static;

    public function has(CacheKey|string $key): bool;

    /**
     * @param iterable<CacheKey|string> $keys
     * @return array<string, mixed>
     */
    public function getMultiple(iterable $keys, mixed $default = null): array;

    /**
     * @param iterable<CacheKey|string, mixed> $values
     */
    public function setMultiple(
        iterable $values,
        CacheTtl|DateInterval|DateTimeInterface|int|null $ttl = null,
    ): static;

    /**
     * @param iterable<CacheKey|string> $keys
     */
    public function deleteMultiple(iterable $keys): static;

    public function remember(
        CacheKey|string $key,
        callable $resolver,
        CacheTtl|DateInterval|DateTimeInterface|int|null $ttl = null,
    ): mixed;

    public function pull(CacheKey|string $key, mixed $default = null): mixed;
}
