<?php

declare(strict_types=1);

namespace CommonPHP\Cache\Tests\Unit;

use CommonPHP\Cache\CacheKey;
use CommonPHP\Cache\Exceptions\InvalidCacheKeyException;
use PHPUnit\Framework\TestCase;
use Stringable;

final class CacheKeyTest extends TestCase
{
    public function testItNormalizesAndComparesKeys(): void
    {
        $key = new CacheKey(' users:1 ');

        self::assertInstanceOf(Stringable::class, $key);
        self::assertSame('users:1', $key->value());
        self::assertSame('users:1', (string) $key);
        self::assertTrue($key->equals('users:1'));
        self::assertSame($key, CacheKey::from($key));
        self::assertSame('tenant.users:1', $key->prefixed('tenant')->value());
    }

    public function testItAcceptsStringableKeysAndPrefixes(): void
    {
        $stringable = new class implements Stringable {
            public function __toString(): string
            {
                return ' stringable:key ';
            }
        };

        $key = CacheKey::from($stringable);

        self::assertSame('stringable:key', $key->value());
        self::assertSame('tenant/stringable:key', $key->prefixed(' tenant ', '/')->value());
        self::assertFalse($key->equals('other'));
    }

    public function testItAllowsMaximumLengthKeys(): void
    {
        $key = new CacheKey(str_repeat('a', CacheKey::MAX_LENGTH));

        self::assertSame(CacheKey::MAX_LENGTH, strlen($key->value()));
    }

    public function testItRejectsEmptyKeys(): void
    {
        $this->expectException(InvalidCacheKeyException::class);

        new CacheKey('   ');
    }

    public function testItRejectsControlCharacters(): void
    {
        $this->expectException(InvalidCacheKeyException::class);

        new CacheKey("users\n1");
    }

    public function testItRejectsOverlongKeys(): void
    {
        $this->expectException(InvalidCacheKeyException::class);

        new CacheKey(str_repeat('a', CacheKey::MAX_LENGTH + 1));
    }

    public function testItRejectsEmptyPrefixes(): void
    {
        $this->expectException(InvalidCacheKeyException::class);

        (new CacheKey('key'))->prefixed(' ');
    }
}
