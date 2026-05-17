<?php

declare(strict_types=1);

namespace CommonPHP\Cache;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;

final readonly class CacheItem
{
    private CacheKey $key;

    private CacheTtl $ttl;

    private DateTimeImmutable $createdAt;

    public function __construct(
        CacheKey|string $key,
        private string $payload,
        CacheTtl|DateInterval|DateTimeInterface|int|null $ttl = null,
        ?DateTimeImmutable $createdAt = null,
    ) {
        $this->key = CacheKey::from($key);
        $this->ttl = CacheTtl::from($ttl);
        $this->createdAt = $createdAt ?? new DateTimeImmutable();
    }

    public static function create(
        CacheKey|string $key,
        string $payload,
        CacheTtl|DateInterval|DateTimeInterface|int|null $ttl = null,
    ): self {
        return new self($key, $payload, $ttl);
    }

    public function key(): CacheKey
    {
        return $this->key;
    }

    public function keyName(): string
    {
        return $this->key->value();
    }

    public function payload(): string
    {
        return $this->payload;
    }

    public function ttl(): CacheTtl
    {
        return $this->ttl;
    }

    public function createdAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function expiresAt(): ?DateTimeImmutable
    {
        return $this->ttl->expiresAt();
    }

    public function isExpired(?DateTimeInterface $now = null): bool
    {
        return $this->ttl->isExpired($now);
    }

    public function secondsRemaining(?DateTimeInterface $now = null): ?int
    {
        return $this->ttl->secondsRemaining($now);
    }

    public function withPayload(string $payload): self
    {
        return new self($this->key, $payload, $this->ttl, $this->createdAt);
    }

    public function withTtl(CacheTtl|DateInterval|DateTimeInterface|int|null $ttl): self
    {
        return new self($this->key, $this->payload, $ttl, $this->createdAt);
    }

}
