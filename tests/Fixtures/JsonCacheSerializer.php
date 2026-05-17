<?php

declare(strict_types=1);

namespace CommonPHP\Cache\Tests\Fixtures;

use CommonPHP\Cache\Contracts\CacheSerializerInterface;
use JsonException;

final class JsonCacheSerializer implements CacheSerializerInterface
{
    /**
     * @throws JsonException
     */
    public function serialize(mixed $value): string
    {
        return json_encode($value, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws JsonException
     */
    public function unserialize(string $payload): mixed
    {
        return json_decode($payload, true, flags: JSON_THROW_ON_ERROR);
    }
}
