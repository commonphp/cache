# Keys And TTL

Cache keys and TTL values are explicit value objects so drivers can receive normalized, predictable data.

## Cache Keys

`CacheKey` accepts strings and `Stringable` objects.

```php
use CommonPHP\Cache\CacheKey;

$key = new CacheKey(' users.42 ');

echo $key->value(); // users.42
echo (string) $key; // users.42
```

Keys are trimmed and validated:

- keys cannot be empty;
- keys cannot contain ASCII control characters;
- keys cannot exceed 512 bytes.

## Prefixing Keys

```php
$tenantKey = (new CacheKey('settings'))->prefixed('tenant.42');

echo $tenantKey->value(); // tenant.42.settings
```

Use prefixes for tenant, module, or feature namespaces when the storage driver does not provide namespacing itself.

## TTL Inputs

The manager accepts these TTL inputs:

- `null` for forever;
- integer seconds;
- `DateInterval`;
- `DateTimeInterface`;
- `CacheTtl`.

```php
$cache->set('short', 'value', 30);
$cache->set('until-midnight', 'value', new DateTimeImmutable('tomorrow midnight'));
$cache->set('forever', 'value');
```

## Expiration Rules

`0` and negative integer TTLs are expired immediately. When `CacheManager::set()` receives an already-expired TTL, it deletes the key instead of storing stale data.

Drivers should also avoid returning expired items. The manager still checks returned items and deletes stale keys if a driver returns an expired `CacheItem`.
