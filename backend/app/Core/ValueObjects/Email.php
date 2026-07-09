<?php

declare(strict_types=1);

namespace App\Core\ValueObjects;

use InvalidArgumentException;

final readonly class Email
{
    public function __construct(
        public string $value,
    ) {
        $email = strtolower(trim($value));

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException(
                'Invalid email address.'
            );
        }

        $this->value = $email;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}