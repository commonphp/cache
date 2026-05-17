# Custom Driver

Custom drivers implement `CacheDriverInterface`. Extending `AbstractCacheDriver` is optional, but it provides key normalization and a default driver name.

```php
<?php

declare(strict_types=1);

use CommonPHP\Cache\CacheItem;
use CommonPHP\Cache\CacheKey;
use CommonPHP\Cache\Contracts\AbstractCacheDriver;
use CommonPHP\Cache\Exceptions\CacheStorageException;

final class FileCacheDriver extends AbstractCacheDriver
{
    public function __construct(
        private string $directory,
    ) {
    }

    public function fetch(CacheKey|string $key): ?CacheItem
    {
        $key = $this->key($key);
        $path = $this->path($key);

        if (!is_file($path)) {
            return null;
        }

        $item = unserialize((string) file_get_contents($path), ['allowed_classes' => true]);

        if (!$item instanceof CacheItem || $item->isExpired()) {
            $this->delete($key);

            return null;
        }

        return $item;
    }

    public function store(CacheItem $item): void
    {
        if (!is_dir($this->directory) && !mkdir($this->directory, 0775, true)) {
            throw CacheStorageException::forOperation('create-directory', $this->directory);
        }

        file_put_contents($this->path($item->key()), serialize($item), LOCK_EX);
    }

    public function delete(CacheKey|string $key): void
    {
        $path = $this->path($this->key($key));

        if (is_file($path)) {
            unlink($path);
        }
    }

    public function clear(): void
    {
        foreach (glob($this->directory . '/*.cache') ?: [] as $path) {
            unlink($path);
        }
    }

    public function has(CacheKey|string $key): bool
    {
        return $this->fetch($key) !== null;
    }

    private function path(CacheKey $key): string
    {
        return $this->directory . '/' . hash('sha256', $key->value()) . '.cache';
    }
}
```

Real driver packages should add backend-specific tests for TTL behavior, connection failures, unavailable storage, permissions, serialization limits, and clear/delete semantics.
