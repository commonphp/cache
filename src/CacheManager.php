<?php

declare(strict_types=1);

namespace CommonPHP\Cache;

use CommonPHP\Cache\Contracts\CacheDriverInterface;
use CommonPHP\Cache\Contracts\CacheInterface;
use CommonPHP\Cache\Contracts\CacheSerializerInterface;
use CommonPHP\Cache\Drivers\ArrayCacheDriver;
use CommonPHP\Cache\Exceptions\CacheDriverException;
use CommonPHP\Cache\Exceptions\CacheException;
use CommonPHP\Cache\Exceptions\InvalidCacheKeyException;
use DateInterval;
use DateTimeInterface;
use Throwable;

final class CacheManager implements CacheInterface
{
    public function __construct(
        private CacheDriverInterface $driver = new ArrayCacheDriver(),
        private CacheSerializerInterface $serializer = new NativeCacheSerializer(),
    ) {
    }

    public static function memory(): self
    {
        return new self(new ArrayCacheDriver());
    }

    public function useDriver(CacheDriverInterface $driver): self
    {
        $this->driver = $driver;

        return $this;
    }

    public function getDriver(): CacheDriverInterface
    {
        return $this->driver;
    }

    public function useSerializer(CacheSerializerInterface $serializer): self
    {
        $this->serializer = $serializer;

        return $this;
    }

    public function getSerializer(): CacheSerializerInterface
    {
        return $this->serializer;
    }

    public function get(CacheKey|string $key, mixed $default = null): mixed
    {
        $item = $this->fetchLiveItem($key);

        if ($item === null) {
            return $default;
        }

        return $this->serializer->unserialize($item->payload());
    }

    public function set(
        CacheKey|string $key,
        mixed $value,
        CacheTtl|DateInterval|DateTimeInterface|int|null $ttl = null,
    ): static {
        $key = CacheKey::from($key);
        $ttl = CacheTtl::from($ttl);

        if ($ttl->isExpired()) {
            return $this->delete($key);
        }

        $payload = $this->serializer->serialize($value);
        $item = CacheItem::create($key, $payload, $ttl);

        $this->operate('store', $key, static fn (CacheDriverInterface $driver): mixed => $driver->store($item));

        return $this;
    }

    public function delete(CacheKey|string $key): static
    {
        $key = CacheKey::from($key);

        $this->operate('delete', $key, static fn (CacheDriverInterface $driver): mixed => $driver->delete($key));

        return $this;
    }

    public function clear(): static
    {
        $this->operate('clear', null, static fn (CacheDriverInterface $driver): mixed => $driver->clear());

        return $this;
    }

    public function has(CacheKey|string $key): bool
    {
        return $this->fetchLiveItem($key) !== null;
    }

    public function getMultiple(iterable $keys, mixed $default = null): array
    {
        $values = [];

        foreach ($keys as $key) {
            $cacheKey = CacheKey::from($key);
            $values[$cacheKey->value()] = $this->get($cacheKey, $default);
        }

        return $values;
    }

    public function setMultiple(
        iterable $values,
        CacheTtl|DateInterval|DateTimeInterface|int|null $ttl = null,
    ): static {
        $ttl = CacheTtl::from($ttl);

        foreach ($values as $key => $value) {
            if (!is_string($key)) {
                throw InvalidCacheKeyException::forValue((string) $key, 'Cache value maps must use string keys.');
            }

            $this->set($key, $value, $ttl);
        }

        return $this;
    }

    public function deleteMultiple(iterable $keys): static
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return $this;
    }

    public function remember(
        CacheKey|string $key,
        callable $resolver,
        CacheTtl|DateInterval|DateTimeInterface|int|null $ttl = null,
    ): mixed {
        $cacheKey = CacheKey::from($key);
        $item = $this->fetchLiveItem($cacheKey);

        if ($item !== null) {
            return $this->serializer->unserialize($item->payload());
        }

        $value = $resolver($cacheKey);
        $this->set($cacheKey, $value, $ttl);

        return $value;
    }

    public function pull(CacheKey|string $key, mixed $default = null): mixed
    {
        $cacheKey = CacheKey::from($key);
        $item = $this->fetchLiveItem($cacheKey);

        if ($item === null) {
            return $default;
        }

        $value = $this->serializer->unserialize($item->payload());
        $this->delete($cacheKey);

        return $value;
    }

    private function fetchLiveItem(CacheKey|string $key): ?CacheItem
    {
        $key = CacheKey::from($key);

        $item = $this->operate(
            'fetch',
            $key,
            static fn (CacheDriverInterface $driver): ?CacheItem => $driver->fetch($key),
        );

        if (!$item instanceof CacheItem) {
            return null;
        }

        if (!$item->isExpired()) {
            return $item;
        }

        $this->delete($key);

        return null;
    }

    private function operate(string $operation, ?CacheKey $key, callable $callback): mixed
    {
        try {
            return $callback($this->driver);
        } catch (CacheException $exception) {
            throw $exception;
        } catch (Throwable $exception) {
            throw CacheDriverException::forOperation($operation, $key?->value(), $exception);
        }
    }
}
