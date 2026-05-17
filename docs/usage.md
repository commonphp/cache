# Usage

The package has three common usage styles: direct `CacheManager` use, bulk operations, and custom drivers or serializers.

## Cache Manager

```php
use CommonPHP\Cache\CacheManager;

$cache = CacheManager::memory();

$cache->set('settings.theme', 'dark');

if ($cache->has('settings.theme')) {
    $theme = $cache->get('settings.theme');
}
```

`CacheManager::memory()` is a named constructor for an in-memory cache. `new CacheManager()` uses the same default driver.

## Defaults

```php
$value = $cache->get('missing', 'fallback');
```

Cache misses are normal control flow and return the default value. Exceptions are reserved for invalid keys, serialization failures, or driver/storage failures.

## Bulk Operations

```php
$cache->setMultiple([
    'feature.search' => true,
    'feature.billing' => false,
], 600);

$features = $cache->getMultiple([
    'feature.search',
    'feature.billing',
    'feature.unknown',
], false);

$cache->deleteMultiple([
    'feature.search',
    'feature.billing',
]);
```

Bulk writes require string keys. Bulk reads return an array keyed by normalized cache key.

## Pull Values

```php
$token = $cache->pull('password-reset.42');
```

`pull()` is useful for one-time values. It returns the cached value, then deletes it. If the key is absent, it returns the default.

## Swap Drivers

```php
use CommonPHP\Cache\Contracts\CacheDriverInterface;

/** @var CacheDriverInterface $driver */
$cache->useDriver($driver);
```

Concrete integrations such as Symfony Cache, Redis, filesystem, database, or Memcached should live outside this core package and implement `CacheDriverInterface`.

## Swap Serializers

```php
use CommonPHP\Cache\Contracts\CacheSerializerInterface;

/** @var CacheSerializerInterface $serializer */
$cache->useSerializer($serializer);
```

The default `NativeCacheSerializer` uses PHP's native `serialize()` and `unserialize()` behavior.
