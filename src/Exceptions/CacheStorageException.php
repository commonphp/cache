<?php

declare(strict_types=1);

namespace CommonPHP\Cache\Exceptions;

use Throwable;

class CacheStorageException extends CacheException
{
    public static function forOperation(string $operation, ?string $key = null, ?Throwable $previous = null): self
    {
        $target = $key === null ? '' : ' for key "' . $key . '"';

        return new self('Cache storage operation "' . $operation . '" failed' . $target . '.', previous: $previous);
    }

}
