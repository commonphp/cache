<?php

declare(strict_types=1);

namespace CommonPHP\Cache\Tests\Unit;

use CommonPHP\Cache\CacheItem;
use CommonPHP\Cache\Contracts\CacheDriverInterface;
use CommonPHP\Cache\Drivers\ArrayCacheDriver;
use CommonPHP\Runtime\Contracts\DriverInterface;
use PHPUnit\Framework\TestCase;

final class ArrayCacheDriverTest extends TestCase
{
    public function testItStoresFetchesAndChecksItems(): void
    {
        $driver = new ArrayCacheDriver();
        $item = CacheItem::create('key', 'payload', 60);

        self::assertInstanceOf(CacheDriverInterface::class, $driver);
        self::assertInstanceOf(DriverInterface::class, $driver);
        self::assertFalse($driver->has('key'));
        self::assertNull($driver->fetch('key'));

        $driver->store($item);

        self::assertTrue($driver->has('key'));
        self::assertSame($item, $driver->fetch('key'));
        self::assertSame(['key' => $item], $driver->all());
    }

    public function testItDeletesAndClearsItems(): void
    {
        $driver = new ArrayCacheDriver();
        $driver->store(CacheItem::create('one', '1'));
        $driver->store(CacheItem::create('two', '2'));

        $driver->delete('one');

        self::assertFalse($driver->has('one'));
        self::assertTrue($driver->has('two'));

        $driver->clear();

        self::assertSame([], $driver->all());
    }

    public function testItPrunesExpiredItemsWhenReading(): void
    {
        $driver = new ArrayCacheDriver();
        $driver->store(CacheItem::create('expired', 'payload', 0));

        self::assertNull($driver->fetch('expired'));
        self::assertFalse($driver->has('expired'));
        self::assertSame([], $driver->all());
    }

    public function testStoringExpiredItemRemovesExistingItem(): void
    {
        $driver = new ArrayCacheDriver();
        $driver->store(CacheItem::create('key', 'fresh', 60));
        $driver->store(CacheItem::create('key', 'stale', 0));

        self::assertFalse($driver->has('key'));
        self::assertSame([], $driver->all());
    }
}
