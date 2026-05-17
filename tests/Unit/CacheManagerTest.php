<?php

declare(strict_types=1);

namespace CommonPHP\Cache\Tests\Unit;

use CommonPHP\Cache\CacheManager;
use CommonPHP\Cache\CacheItem;
use CommonPHP\Cache\CacheKey;
use CommonPHP\Cache\Contracts\CacheInterface;
use CommonPHP\Cache\Drivers\ArrayCacheDriver;
use CommonPHP\Cache\Exceptions\CacheDriverException;
use CommonPHP\Cache\Exceptions\CacheSerializationException;
use CommonPHP\Cache\Exceptions\CacheStorageException;
use CommonPHP\Cache\Exceptions\InvalidCacheKeyException;
use CommonPHP\Cache\NativeCacheSerializer;
use CommonPHP\Cache\Tests\Fixtures\ExposedCacheDriver;
use CommonPHP\Cache\Tests\Fixtures\FailingCacheSerializer;
use CommonPHP\Cache\Tests\Fixtures\JsonCacheSerializer;
use CommonPHP\Cache\Tests\Fixtures\ThrowingCacheDriver;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class CacheManagerTest extends TestCase
{
    public function testItStoresReadsAndDeletesValues(): void
    {
        $cache = new CacheManager();

        self::assertInstanceOf(CacheInterface::class, $cache);
        self::assertInstanceOf(ArrayCacheDriver::class, $cache->getDriver());
        self::assertInstanceOf(NativeCacheSerializer::class, $cache->getSerializer());

        $result = $cache->set('user:1', ['name' => 'Ada'], 60);

        self::assertSame($cache, $result);
        self::assertTrue($cache->has('user:1'));
        self::assertSame(['name' => 'Ada'], $cache->get('user:1'));
        self::assertSame('fallback', $cache->get('missing', 'fallback'));

        $cache->delete('user:1');

        self::assertFalse($cache->has('user:1'));
    }

    public function testItCanCreateAnInMemoryCache(): void
    {
        $cache = CacheManager::memory();

        $cache->set('key', 'value');

        self::assertSame('value', $cache->get('key'));
    }

    public function testExpiredValuesAreRemoved(): void
    {
        $driver = new ArrayCacheDriver();
        $cache = new CacheManager($driver);

        $cache->set('expired', 'old', 60);
        $cache->set('expired', 'value', 0);

        self::assertFalse($cache->has('expired'));
        self::assertSame('fallback', $cache->get('expired', 'fallback'));
        self::assertSame([], $driver->all());
    }

    public function testExpiredItemsReturnedByDriversAreDeleted(): void
    {
        $driver = new ExposedCacheDriver();
        $driver->item = CacheItem::create('stale', (new NativeCacheSerializer())->serialize('value'), 0);
        $cache = new CacheManager($driver);

        self::assertSame('fallback', $cache->get('stale', 'fallback'));
        self::assertSame(['stale'], $driver->deleted);
    }

    public function testMultipleOperationsUseKeyedArrays(): void
    {
        $cache = new CacheManager();

        $cache->setMultiple([
            'one' => 1,
            'two' => 2,
        ]);

        self::assertSame([
            'one' => 1,
            'two' => 2,
            'missing' => 'fallback',
        ], $cache->getMultiple(['one', 'two', 'missing'], 'fallback'));

        $cache->deleteMultiple(['one', 'two']);

        self::assertFalse($cache->has('one'));
        self::assertFalse($cache->has('two'));
    }

    public function testMultipleOperationsAcceptIterablesAndCacheKeys(): void
    {
        $cache = new CacheManager();
        $values = (static function (): iterable {
            yield 'first' => 'a';
            yield 'second' => 'b';
        })();

        $cache->setMultiple($values);

        $keys = (static function (): iterable {
            yield new CacheKey('first');
            yield new CacheKey('second');
        })();

        self::assertSame([
            'first' => 'a',
            'second' => 'b',
        ], $cache->getMultiple($keys));

        $cache->deleteMultiple((static function (): iterable {
            yield new CacheKey('first');
            yield 'second';
        })());

        self::assertSame([
            'first' => null,
            'second' => null,
        ], $cache->getMultiple(['first', 'second']));
    }

    public function testSetMultipleRequiresStringKeys(): void
    {
        $cache = new CacheManager();

        $this->expectException(InvalidCacheKeyException::class);

        $cache->setMultiple([1 => 'numeric']);
    }

    public function testRememberOnlyResolvesMissingValues(): void
    {
        $cache = new CacheManager();
        $calls = 0;
        $resolvedKey = null;

        $first = $cache->remember('computed', function (CacheKey $key) use (&$calls, &$resolvedKey): string {
            ++$calls;
            $resolvedKey = $key;

            return 'value';
        });
        $second = $cache->remember('computed', function () use (&$calls): string {
            ++$calls;

            return 'changed';
        });

        self::assertSame('value', $first);
        self::assertSame('value', $second);
        self::assertSame(1, $calls);
        self::assertInstanceOf(CacheKey::class, $resolvedKey);
        self::assertSame('computed', $resolvedKey->value());
    }

    public function testPullReturnsAndRemovesCachedValue(): void
    {
        $cache = new CacheManager();

        $cache->set('token', 'abc');

        self::assertSame('abc', $cache->pull('token'));
        self::assertSame('fallback', $cache->pull('token', 'fallback'));
    }

    public function testClearRemovesAllValues(): void
    {
        $cache = new CacheManager();
        $cache->set('one', 1);
        $cache->set('two', 2);

        self::assertSame($cache, $cache->clear());

        self::assertFalse($cache->has('one'));
        self::assertFalse($cache->has('two'));
    }

    public function testDriverAndSerializerCanBeReplaced(): void
    {
        $cache = new CacheManager();
        $driver = new ArrayCacheDriver();
        $serializer = new NativeCacheSerializer();

        self::assertSame($cache, $cache->useDriver($driver));
        self::assertSame($driver, $cache->getDriver());
        self::assertSame($cache, $cache->useSerializer($serializer));
        self::assertSame($serializer, $cache->getSerializer());
    }

    public function testCustomSerializerCanBeUsed(): void
    {
        $cache = new CacheManager(serializer: new JsonCacheSerializer());

        $cache->set('json', ['answer' => 42]);

        self::assertSame(['answer' => 42], $cache->get('json'));
    }

    public function testSerializerExceptionsBubbleOut(): void
    {
        $exception = new CacheSerializationException('serializer failed');
        $cache = new CacheManager(serializer: new FailingCacheSerializer($exception));

        $this->expectExceptionObject($exception);

        $cache->set('key', 'value');
    }

    public function testSerializerReadExceptionsBubbleOut(): void
    {
        $exception = new CacheSerializationException('serializer failed');
        $driver = new ArrayCacheDriver();
        $driver->store(CacheItem::create('key', 'payload'));
        $cache = new CacheManager($driver, new FailingCacheSerializer($exception));

        $this->expectExceptionObject($exception);

        $cache->get('key');
    }

    public function testDriverRuntimeFailuresAreWrapped(): void
    {
        $cache = new CacheManager(new ThrowingCacheDriver(new RuntimeException('driver down')));

        $this->expectException(CacheDriverException::class);
        $this->expectExceptionMessage('fetch');

        $cache->get('key');
    }

    public function testDriverStoreFailuresAreWrapped(): void
    {
        $cache = new CacheManager(new ThrowingCacheDriver(new RuntimeException('driver down')));

        $this->expectException(CacheDriverException::class);
        $this->expectExceptionMessage('store');

        $cache->set('key', 'value');
    }

    public function testDriverDeleteFailuresAreWrapped(): void
    {
        $cache = new CacheManager(new ThrowingCacheDriver(new RuntimeException('driver down')));

        $this->expectException(CacheDriverException::class);
        $this->expectExceptionMessage('delete');

        $cache->delete('key');
    }

    public function testDriverClearFailuresAreWrapped(): void
    {
        $cache = new CacheManager(new ThrowingCacheDriver(new RuntimeException('driver down')));

        $this->expectException(CacheDriverException::class);
        $this->expectExceptionMessage('clear');

        $cache->clear();
    }

    public function testCacheExceptionsFromDriversAreNotWrappedAgain(): void
    {
        $exception = new CacheStorageException('storage failed');
        $cache = new CacheManager(new ThrowingCacheDriver($exception));

        $this->expectExceptionObject($exception);

        $cache->get('key');
    }
}
