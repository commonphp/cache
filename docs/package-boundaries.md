# Package Boundaries

CommonPHP Cache owns the core cache API, value objects, serialization contract, driver contract, and a simple in-memory driver.

## Belongs Here

- Cache key normalization and validation.
- TTL normalization and expiration checks.
- Cache item representation.
- Cache manager behavior.
- Cache driver and serializer contracts.
- Native serialization.
- In-memory storage for tests and request-local usage.
- Cache-specific exceptions.

## Does Not Belong Here

- Symfony Cache adapter wiring.
- Redis, Memcached, database, or filesystem storage implementations.
- HTTP cache headers or asset cache policy.
- Session storage.
- Runtime service provider registration.
- CLI cache clear commands.
- Encryption, compression, or application-specific serialization formats.

Those concerns should live in driver packages or higher-level integrations and call Cache through `CacheInterface` or `CacheDriverInterface`.

## Integration Shape

Applications should depend on `CacheInterface` where they only need cache behavior. Infrastructure packages should provide concrete drivers and inject them into `CacheManager`.

The core package should remain easy to understand and debug: manager code handles workflow, drivers handle storage, serializers handle payload format.
