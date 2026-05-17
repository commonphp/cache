<?php

declare(strict_types=1);

namespace CommonPHP\Cache\Tests\Unit;

use CommonPHP\Cache\CacheItem;
use CommonPHP\Cache\CacheKey;
use CommonPHP\Cache\Tests\Fixtures\ExposedCacheDriver;
use PHPUnit\Framework\TestCase;

final class AbstractCacheDriverTest extends TestCase
{
    public function testItProvidesADefaultDriverName(): void
    {
        $driver = new ExposedCacheDriver();

        self::assertSame(ExposedCacheDriver::class, $driver->getName());
    }

    public function testItNormalizesKeysForDrivers(): void
    {
        $driver = new ExposedCacheDriver();
        $key = $driver->exposeKey(' cache.key ');

        self::assertInstanceOf(CacheKey::class, $key);
        self::assertSame('cache.key', $key->value());
    }

    public function testItDetectsLiveItems(): void
    {
        $driver = new ExposedCacheDriver();
        $live = CacheItem::create('live', 'payload', 60);
        $expired = CacheItem::create('expired', 'payload', 0);

        self::assertTrue($driver->exposeIsLive($live));
        self::assertFalse($driver->exposeIsLive($expired));
        self::assertFalse($driver->exposeIsLive(null));
    }
}
