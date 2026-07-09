<?php

declare(strict_types=1);

namespace App\Core\ValueObjects;

use Illuminate\Support\Str;
use InvalidArgumentException;

final readonly class UUID
{
    public function __construct(
        public string $value,
    ) {
        if (! Str::isUuid($value)) {
            throw new InvalidArgumentException(
                'Invalid UUID supplied.'
            );
        }
    }

    public static function generate(): self
    {
        return new self(
            (string) Str::uuid()
        );
    }

    public function __toString(): string
    {
        return $this->value;
    }
}