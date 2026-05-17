<?php

declare(strict_types=1);

namespace CommonPHP\Cache\Tests\Unit;

use CommonPHP\Cache\CacheItem;
use CommonPHP\Cache\CacheKey;
use CommonPHP\Cache\CacheTtl;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class CacheItemTest extends TestCase
{
    public function testItExposesKeyPayloadTtlAndTimestamps(): void
    {
        $createdAt = new DateTimeImmutable('2026-05-17 12:00:00');
        $expiresAt = new DateTimeImmutable('2026-05-17 12:01:00');
        $ttl = CacheTtl::until($expiresAt);
        $item = new CacheItem(new CacheKey('key'), 'payload', $ttl, $createdAt);

        self::assertSame('key', $item->keyName());
        self::assertSame('key', $item->key()->value());
        self::assertSame('payload', $item->payload());
        self::assertSame($ttl, $item->ttl());
        self::assertSame($createdAt, $item->createdAt());
        self::assertEquals($expiresAt, $item->expiresAt());
        self::assertFalse($item->isExpired($createdAt));
        self::assertSame(60, $item->secondsRemaining($createdAt));
    }

    public function testFactoryCreatesItemsFromStringKeys(): void
    {
        $item = CacheItem::create('key', 'payload');

        self::assertSame('key', $item->keyName());
        self::assertSame('payload', $item->payload());
        self::assertTrue($item->ttl()->isForever());
        self::assertNull($item->expiresAt());
    }

    public function testItCreatesModifiedCopies(): void
    {
        $createdAt = new DateTimeImmutable('2026-05-17 12:00:00');
        $item = new CacheItem('key', 'payload', null, $createdAt);

        $withPayload = $item->withPayload('changed');
        $withTtl = $item->withTtl(0);

        self::assertNotSame($item, $withPayload);
        self::assertSame('changed', $withPayload->payload());
        self::assertSame('payload', $item->payload());
        self::assertSame($createdAt, $withPayload->createdAt());
        self::assertTrue($withTtl->isExpired());
        self::assertSame('payload', $withTtl->payload());
    }
}
