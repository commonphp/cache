<?php

declare(strict_types=1);

namespace CommonPHP\Cache;

use DateInterval;
use DateTimeImmutable;
use DateTimeInterface;

final readonly class CacheTtl
{
    public function __construct(
        private ?DateTimeImmutable $expiresAt = null,
    ) {
    }

    public static function forever(): self
    {
        return new self();
    }

    public static function seconds(int $seconds, ?DateTimeImmutable $now = null): self
    {
        $now ??= new DateTimeImmutable();

        return new self($now->modify(($seconds <= 0 ? '0' : '+' . $seconds) . ' seconds'));
    }

    public static function until(DateTimeInterface $expiresAt): self
    {
        return new self(DateTimeImmutable::createFromInterface($expiresAt));
    }

    public static function interval(DateInterval $ttl, ?DateTimeImmutable $now = null): self
    {
        $now ??= new DateTimeImmutable();

        return new self($now->add($ttl));
    }

    public static function expired(?DateTimeImmutable $now = null): self
    {
        return new self($now ?? new DateTimeImmutable());
    }

    public static function from(self|DateInterval|DateTimeInterface|int|null $ttl): self
    {
        return match (true) {
            $ttl instanceof self => $ttl,
            $ttl instanceof DateInterval => self::interval($ttl),
            $ttl instanceof DateTimeInterface => self::until($ttl),
            is_int($ttl) => self::seconds($ttl),
            default => self::forever(),
        };
    }

    public function expiresAt(): ?DateTimeImmutable
    {
        return $this->expiresAt;
    }

    public function isForever(): bool
    {
        return $this->expiresAt === null;
    }

    public function isExpired(?DateTimeInterface $now = null): bool
    {
        if ($this->expiresAt === null) {
            return false;
        }

        $now = $now === null
            ? new DateTimeImmutable()
            : DateTimeImmutable::createFromInterface($now);

        return $this->expiresAt <= $now;
    }

    public function secondsRemaining(?DateTimeInterface $now = null): ?int
    {
        if ($this->expiresAt === null) {
            return null;
        }

        $now = $now === null
            ? new DateTimeImmutable()
            : DateTimeImmutable::createFromInterface($now);

        return max(0, $this->expiresAt->getTimestamp() - $now->getTimestamp());
    }

}
