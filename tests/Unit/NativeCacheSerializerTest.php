<?php

declare(strict_types=1);

namespace CommonPHP\Cache\Tests\Unit;

use CommonPHP\Cache\Exceptions\CacheSerializationException;
use CommonPHP\Cache\NativeCacheSerializer;
use PHPUnit\Framework\TestCase;

final class NativeCacheSerializerTest extends TestCase
{
    public function testItRoundTripsValues(): void
    {
        $serializer = new NativeCacheSerializer();
        $payload = $serializer->serialize([
            'name' => 'Ada',
            'active' => true,
        ]);

        self::assertSame([
            'name' => 'Ada',
            'active' => true,
        ], $serializer->unserialize($payload));
        self::assertFalse($serializer->unserialize($serializer->serialize(false)));
    }

    public function testItRoundTripsNullScalarsObjectsAndResourcesAsNativeSerializeAllows(): void
    {
        $serializer = new NativeCacheSerializer();
        $object = new \stdClass();
        $object->name = 'Ada';

        self::assertNull($serializer->unserialize($serializer->serialize(null)));
        self::assertSame(123, $serializer->unserialize($serializer->serialize(123)));
        self::assertSame('text', $serializer->unserialize($serializer->serialize('text')));
        self::assertEquals($object, $serializer->unserialize($serializer->serialize($object)));
    }

    public function testItRejectsInvalidPayloads(): void
    {
        $serializer = new NativeCacheSerializer();

        $this->expectException(CacheSerializationException::class);

        $serializer->unserialize('not a serialized payload');
    }
}
