<?php

declare(strict_types=1);

namespace App\Core\ValueObjects;

use Illuminate\Support\Str;
use InvalidArgumentException;

final readonly class UUID
{
    public string $value;

    public function __construct(string $uuid)
    {
        if (! Str::isUuid($uuid)) {
            throw new InvalidArgumentException('Invalid UUID.');
        }

        $this->value = $uuid;
    }

    public static function generate(): self
    {
        return new self((string) Str::uuid());
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $uuid): bool
    {
        return $this->value === $uuid->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}