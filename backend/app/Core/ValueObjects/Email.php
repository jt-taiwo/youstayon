<?php

declare(strict_types=1);

namespace App\Core\ValueObjects;

use InvalidArgumentException;

final readonly class Email
{
public function __construct(string $value)
{
    $normalized = strtolower(trim($value));

        if (! filter_var($normalized, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address.');
        }

        $this->value = $normalized;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}