<?php

declare(strict_types=1);

namespace CommonPHP\Cache\Exceptions;

use Throwable;

class CacheSerializationException extends CacheException
{
    public static function forOperation(string $operation, ?Throwable $previous = null): self
    {
        return new self('Cache serialization operation "' . $operation . '" failed.', previous: $previous);
    }

    public static function forPayload(string $payload): self
    {
        return new self('Cache payload could not be unserialized. Payload size: ' . strlen($payload) . ' bytes.');
    }

}
