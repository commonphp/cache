<?php

declare(strict_types=1);

namespace CommonPHP\Cache;

use CommonPHP\Cache\Contracts\CacheSerializerInterface;
use CommonPHP\Cache\Exceptions\CacheSerializationException;
use Throwable;

final class NativeCacheSerializer implements CacheSerializerInterface
{
    public function serialize(mixed $value): string
    {
        try {
            return serialize($value);
        } catch (Throwable $exception) {
            throw CacheSerializationException::forOperation('serialize', $exception);
        }
    }

    public function unserialize(string $payload): mixed
    {
        try {
            $value = @unserialize($payload, ['allowed_classes' => true]);
        } catch (Throwable $exception) {
            throw CacheSerializationException::forOperation('unserialize', $exception);
        }

        if ($value === false && $payload !== serialize(false)) {
            throw CacheSerializationException::forPayload($payload);
        }

        return $value;
    }

}
