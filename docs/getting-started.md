# Getting Started

CommonPHP Cache stores values behind a small manager API. The default manager uses the package's in-memory `ArrayCacheDriver`, which is useful for request-local caching, tests, and examples.

## Install

```bash
composer require comphp/cache
```

In this monorepo, the package is also available through the workspace path repository and the root Composer autoloader.

## Store And Read Values

```php
<?php

declare(strict_types=1);

use CommonPHP\Cache\CacheManager;

$cache = new CacheManager();

$cache->set('users.ada', [
    'name' => 'Ada Lovelace',
], 300);

$user = $cache->get('users.ada', []);
```

The third argument to `set()` is the TTL. Integers are seconds. `null` means store forever.

## Cache Missing Work

```php
$profile = $cache->remember('profile.42', function () use ($repository): array {
    return $repository->findProfile(42);
}, 300);
```

`remember()` returns the cached value if it exists. If the key is missing or expired, it calls the resolver, stores the result, and returns it.

## Remove Values

```php
$token = $cache->pull('reset-token.42');

$cache->delete('profile.42');
$cache->clear();
```

`pull()` reads and deletes a value in one call. Missing values return the provided default, or `null` when no default is provided.
