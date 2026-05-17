# CommonPHP Cache Documentation

CommonPHP Cache is the small cache package for CommonPHP applications and plain PHP projects. It provides a cache manager, cache keys, TTL handling, serialized cache items, driver contracts, and an in-memory driver without depending on Symfony Cache, Redis, filesystem, database, HTTP, routing, sessions, or runtime bootstrapping.

The package is intentionally driver-oriented. The manager owns the user-facing cache workflow; drivers own storage; serializers own value encoding.

## Start Here

- [Getting started](getting-started.md)
- [Usage](usage.md)
- [Package boundaries](package-boundaries.md)

## Cache Concepts

- [Keys and TTL](keys-and-ttl.md)
- [Items and serialization](items-and-serialization.md)
- [Drivers and manager](drivers-and-manager.md)
- [Error handling](error-handling.md)

## Examples

- [Examples index](examples/index.md)
- [Basic cache usage](examples/basic-cache.md)
- [Custom driver](examples/custom-driver.md)

## Development

- [Testing and QA](testing.md)

## Public API Map

Entry points:

- `CommonPHP\Cache\CacheManager`
- `CommonPHP\Cache\Drivers\ArrayCacheDriver`

Value objects:

- `CommonPHP\Cache\CacheKey`
- `CommonPHP\Cache\CacheTtl`
- `CommonPHP\Cache\CacheItem`

Serialization:

- `CommonPHP\Cache\NativeCacheSerializer`
- `CommonPHP\Cache\Contracts\CacheSerializerInterface`

Contracts:

- `CommonPHP\Cache\Contracts\CacheInterface`
- `CommonPHP\Cache\Contracts\CacheDriverInterface`
- `CommonPHP\Cache\Contracts\AbstractCacheDriver`

Exceptions:

- `CommonPHP\Cache\Exceptions\CacheException`
- `CommonPHP\Cache\Exceptions\InvalidCacheKeyException`
- `CommonPHP\Cache\Exceptions\CacheDriverException`
- `CommonPHP\Cache\Exceptions\CacheStorageException`
- `CommonPHP\Cache\Exceptions\CacheSerializationException`
