# Error Handling

Cache has four common failure categories.

## Misses And Expiration

Missing and expired values are normal control flow.

```php
$value = $cache->get('missing', 'fallback');
```

Expired values are treated as misses. If the manager sees an expired item from a driver, it asks the driver to delete the key.

## Invalid Keys

Invalid keys throw `InvalidCacheKeyException`.

```php
$cache->get('');
```

Examples include empty keys, control characters, and keys longer than 512 bytes.

## Serialization Failures

Serialization and unserialization failures throw `CacheSerializationException`.

The default serializer throws this exception for invalid native serialized payloads. Custom serializers may throw the same exception when they cannot encode or decode a value.

## Driver And Storage Failures

Drivers should throw CommonPHP cache exceptions for expected storage failures. `CacheManager` rethrows existing `CacheException` instances unchanged.

Unexpected non-cache throwables from a driver are wrapped in `CacheDriverException` with the attempted operation and key.

```php
try {
    $cache->set('key', 'value');
} catch (CacheDriverException $exception) {
    // log driver/storage outage
}
```

Use `CacheStorageException` inside concrete drivers when a backend-specific operation fails and the driver can name the operation.
