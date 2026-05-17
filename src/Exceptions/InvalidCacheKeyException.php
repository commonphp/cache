<?php

declare(strict_types=1);

namespace CommonPHP\Cache\Exceptions;

class InvalidCacheKeyException extends CacheException
{
    public static function forValue(string $key, string $reason): self
    {
        $label = $key === '' ? '<empty>' : $key;

        return new self('Invalid cache key "' . $label . '". ' . $reason);
    }

}
