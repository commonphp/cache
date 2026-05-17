# Basic Cache Usage

```php
<?php

declare(strict_types=1);

use CommonPHP\Cache\CacheManager;

$cache = CacheManager::memory();

$cache->set('dashboard.stats', [
    'users' => 120,
    'invoices' => 14,
], 60);

$stats = $cache->remember('dashboard.stats', function (): array {
    return [
        'users' => 120,
        'invoices' => 14,
    ];
}, 60);

$token = $cache->pull('password-reset.42', null);

$cache->delete('dashboard.stats');
```

Use the in-memory driver for tests, examples, and request-local memoization. Use a concrete external driver package when values must survive across PHP processes.
