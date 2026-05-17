<?php

declare(strict_types=1);

namespace CommonPHP\Cache\Tests\Unit;

use CommonPHP\Cache\Exceptions\CacheDriverException;
use CommonPHP\Cache\Exceptions\CacheException;
use CommonPHP\Cache\Exceptions\CacheSerializationException;
use CommonPHP\Cache\Exceptions\CacheStorageException;
use CommonPHP\Cache\Exceptions\InvalidCacheKeyException;
use PHPUnit\Framework\TestCase;
use RuntimeException;

final class ExceptionsTest extends TestCase
{
    public function testInvalidKeyExceptionIncludesValueAndReason(): void
    {
        $exception = InvalidCacheKeyException::forValue('', 'Cache keys cannot be empty.');

        self::assertInstanceOf(CacheException::class, $exception);
        self::assertStringContainsString('<empty>', $exception->getMessage());
        self::assertStringContainsString('cannot be empty', $exception->getMessage());
    }

    public function testDriverExceptionFactoryIncludesOperationKeyAndPreviousException(): void
    {
        $previous = new RuntimeException('driver failed');
        $exception = CacheDriverException::forOperation('fetch', 'key', $previous);

        self::assertInstanceOf(CacheException::class, $exception);
        self::assertSame($previous, $exception->getPrevious());
        self::assertStringContainsString('fetch', $exception->getMessage());
        self::assertStringContainsString('key', $exception->getMessage());
    }

    public function testStorageExceptionFactorySupportsOptionalKeys(): void
    {
        $previous = new RuntimeException('storage failed');
        $withKey = CacheStorageException::forOperation('write', 'key', $previous);
        $withoutKey = CacheStorageException::forOperation('clear');

        self::assertSame($previous, $withKey->getPrevious());
        self::assertStringContainsString('write', $withKey->getMessage());
        self::assertStringContainsString('key', $withKey->getMessage());
        self::assertStringContainsString('clear', $withoutKey->getMessage());
    }

    public function testSerializationExceptionFactoriesDescribeFailures(): void
    {
        $previous = new RuntimeException('bad value');
        $operation = CacheSerializationException::forOperation('serialize', $previous);
        $payload = CacheSerializationException::forPayload('broken');

        self::assertSame($previous, $operation->getPrevious());
        self::assertStringContainsString('serialize', $operation->getMessage());
        self::assertStringContainsString('6 bytes', $payload->getMessage());
    }
}
