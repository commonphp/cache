<?php

declare(strict_types=1);

namespace CommonPHP\Cache;

use CommonPHP\Cache\Exceptions\InvalidCacheKeyException;
use Stringable;

final readonly class CacheKey implements Stringable
{
    public const int MAX_LENGTH = 512;

    public string $value;

    public function __construct(string|Stringable $value)
    {
        $this->value = self::normalize((string) $value);
    }

    public static function from(string|Stringable|self $key): self
    {
        return $key instanceof self ? $key : new self($key);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function prefixed(string|Stringable $prefix, string $separator = '.'): self
    {
        $prefix = self::normalize((string) $prefix);

        return new self($prefix . $separator . $this->value);
    }

    public function equals(string|Stringable|self $key): bool
    {
        return $this->value === self::from($key)->value();
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private static function normalize(string $value): string
    {
        $value = trim($value);

        if ($value === '') {
            throw InvalidCacheKeyException::forValue($value, 'Cache keys cannot be empty.');
        }

        if (strlen($value) > self::MAX_LENGTH) {
            throw InvalidCacheKeyException::forValue(
                $value,
                'Cache keys cannot be longer than ' . self::MAX_LENGTH . ' bytes.',
            );
        }

        if (preg_match('/[\x00-\x1F\x7F]/', $value) === 1) {
            throw InvalidCacheKeyException::forValue($value, 'Cache keys cannot contain control characters.');
        }

        return $value;
    }

}
