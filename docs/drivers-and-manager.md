# Drivers And Manager

`CacheManager` is the user-facing API. `CacheDriverInterface` is the storage boundary.

## Manager Responsibilities

The manager:

- normalizes cache keys;
- converts TTL inputs to `CacheTtl`;
- serializes values before storage;
- unserializes values after reads;
- returns defaults for misses;
- removes expired items;
- wraps unexpected driver failures in `CacheDriverException`.

## Driver Responsibilities

Drivers implement `CacheDriverInterface`:

```php
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
```

Drivers should store and return `CacheItem` instances. They should delete or ignore expired items where possible. Storage-specific failures should throw CommonPHP cache exceptions instead of returning ambiguous false values.

## Array Driver

`ArrayCacheDriver` stores items in a PHP array.

```php
use CommonPHP\Cache\CacheManager;
use CommonPHP\Cache\Drivers\ArrayCacheDriver;

$cache = new CacheManager(new ArrayCacheDriver());
```

Use it for tests, examples, request-local memoization, and simple in-process caches. It is not shared across PHP processes.

## Abstract Driver

`AbstractCacheDriver` provides:

- a default `getName()` implementation;
- protected key normalization;
- protected live-item detection.

Extending it is optional but keeps custom drivers consistent.
