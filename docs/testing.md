# Testing And QA

CommonPHP Cache includes a package-local PHPUnit configuration and unit tests.

## Install Dependencies

From the package directory:

```bash
composer install
```

From the monorepo, the root `vendor` directory can also satisfy the test suite because `tests/bootstrap.php` checks both package and workspace autoloaders.

## Run PHPUnit

From the monorepo root:

```bash
vendor/bin/phpunit -c package/cache/phpunit.xml.dist
```

On Windows:

```powershell
vendor\bin\phpunit.bat -c package\cache\phpunit.xml.dist
```

From `package/cache`:

```bash
../../vendor/bin/phpunit -c phpunit.xml.dist
```

## Current Test Coverage

The unit suite covers:

- `CacheKey` normalization, `Stringable` support, prefixing, equality, maximum length, empty keys, overlong keys, and control-character rejection;
- `CacheTtl` forever values, integer seconds, zero and negative seconds, date intervals, inverted intervals, mutable and immutable date times, expiration checks, and remaining seconds;
- `CacheItem` construction, factory creation, key access, payload access, TTL access, creation timestamps, expiration, and immutable copy methods;
- `NativeCacheSerializer` native round trips, false payloads, objects, scalar values, null values, and invalid payload rejection;
- `ArrayCacheDriver` storage, fetches, existence checks, deletion, clearing, expired item pruning, and replacement with expired items;
- `AbstractCacheDriver` default names, key normalization, and live-item detection;
- `CacheManager` default collaborators, memory factory, get/set/delete/clear, default values, expiration cleanup, bulk operations, iterables, `remember()`, `pull()`, driver replacement, serializer replacement, custom serializers, and failure paths;
- cache contracts and runtime driver integration;
- cache exception factories and inheritance.

## Manual Review Areas

Manual review should still cover concrete external drivers, especially distributed stores with their own TTL precision, serialization limits, network failure modes, and key restrictions.
