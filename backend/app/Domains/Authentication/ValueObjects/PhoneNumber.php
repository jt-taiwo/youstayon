<?php

declare(strict_types=1);

namespace App\Domains\Authentication\ValueObjects;

use InvalidArgumentException;

final readonly class PhoneNumber
{
    public string $value;

    public function __construct(string $phone)
    {
        $phone = preg_replace('/\s+/', '', trim($phone));

        if (! preg_match('/^\+?[0-9]{10,15}$/', $phone)) {
            throw new InvalidArgumentException('Invalid phone number.');
        }

        $this->value = $phone;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $phone): bool
    {
        return $this->value === $phone->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}