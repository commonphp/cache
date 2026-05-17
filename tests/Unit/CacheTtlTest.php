<?php

declare(strict_types=1);

namespace CommonPHP\Cache\Tests\Unit;

use CommonPHP\Cache\CacheTtl;
use DateInterval;
use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class CacheTtlTest extends TestCase
{
    public function testForeverTtlNeverExpires(): void
    {
        $ttl = CacheTtl::forever();

        self::assertTrue($ttl->isForever());
        self::assertNull($ttl->expiresAt());
        self::assertNull($ttl->secondsRemaining());
        self::assertFalse($ttl->isExpired());
    }

    public function testSecondsTtlCalculatesExpiration(): void
    {
        $now = new DateTimeImmutable('2026-05-17 12:00:00');
        $ttl = CacheTtl::seconds(30, $now);

        self::assertFalse($ttl->isForever());
        self::assertFalse($ttl->isExpired($now));
        self::assertSame(30, $ttl->secondsRemaining($now));
        self::assertTrue($ttl->isExpired($now->modify('+31 seconds')));
    }

    public function testItAcceptsDateIntervalsAndDateTimes(): void
    {
        $intervalTtl = CacheTtl::from(new DateInterval('PT1S'));
        $absoluteTtl = CacheTtl::from(new DateTimeImmutable('+1 minute'));

        self::assertFalse($intervalTtl->isExpired());
        self::assertFalse($absoluteTtl->isExpired());
    }

    public function testFromReturnsExistingTtlAndForeverForNull(): void
    {
        $ttl = CacheTtl::seconds(5);

        self::assertSame($ttl, CacheTtl::from($ttl));
        self::assertTrue(CacheTtl::from(null)->isForever());
    }

    public function testZeroAndNegativeSecondsAreExpiredImmediately(): void
    {
        $now = new DateTimeImmutable('2026-05-17 12:00:00');

        self::assertTrue(CacheTtl::seconds(0, $now)->isExpired($now));
        self::assertTrue(CacheTtl::seconds(-10, $now)->isExpired($now));
        self::assertSame(0, CacheTtl::seconds(-10, $now)->secondsRemaining($now));
    }

    public function testExpiredFactoryUsesCurrentOrProvidedTime(): void
    {
        $now = new DateTimeImmutable('2026-05-17 12:00:00');
        $ttl = CacheTtl::expired($now);

        self::assertTrue($ttl->isExpired($now));
        self::assertEquals($now, $ttl->expiresAt());
    }

    public function testItConvertsMutableDateTimesForComparisons(): void
    {
        $ttl = CacheTtl::until(new DateTime('2026-05-17 12:00:10'));

        self::assertFalse($ttl->isExpired(new DateTime('2026-05-17 12:00:09')));
        self::assertSame(10, $ttl->secondsRemaining(new DateTime('2026-05-17 12:00:00')));
        self::assertTrue($ttl->isExpired(new DateTime('2026-05-17 12:00:10')));
    }

    public function testInvertedIntervalsCanRepresentExpiredTtls(): void
    {
        $now = new DateTimeImmutable('2026-05-17 12:00:00');
        $interval = new DateInterval('PT5S');
        $interval->invert = 1;

        $ttl = CacheTtl::interval($interval, $now);

        self::assertTrue($ttl->isExpired($now));
        self::assertSame(0, $ttl->secondsRemaining($now));
    }
}
