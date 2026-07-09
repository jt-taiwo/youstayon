<?php

declare(strict_types=1);

namespace App\Domains\Authentication\ValueObjects;

use InvalidArgumentException;

final readonly class Email
{
    public string $value;

    public function __construct(string $email)
    {
        $email = strtolower(trim($email));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email address.');
        }

        $this->value = $email;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $email): bool
    {
        return $this->value === $email->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}