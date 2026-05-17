<?php

declare(strict_types=1);

namespace CommonPHP\Cache\Tests\Unit;

use CommonPHP\Cache\CacheManager;
use CommonPHP\Cache\Contracts\CacheInterface;
use CommonPHP\Cache\Contracts\CacheSerializerInterface;
use CommonPHP\Cache\Drivers\ArrayCacheDriver;
use CommonPHP\Cache\NativeCacheSerializer;
use PHPUnit\Framework\TestCase;

final class ContractsTest extends TestCase
{
    public function testManagerImplementsCacheInterface(): void
    {
        self::assertInstanceOf(CacheInterface::class, new CacheManager());
    }

    public function testDefaultDriverImplementsCacheDriverContract(): void
    {
        $manager = new CacheManager();

        self::assertSame(ArrayCacheDriver::class, $manager->getDriver()::class);
    }

    public function testNativeSerializerImplementsSerializerContract(): void
    {
        self::assertInstanceOf(CacheSerializerInterface::class, new NativeCacheSerializer());
    }
}
