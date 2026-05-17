<?php

declare(strict_types=1);

namespace CommonPHP\Cache\Contracts;

interface CacheSerializerInterface
{
    public function serialize(mixed $value): string;

    public function unserialize(string $payload): mixed;
}
