<?php

declare(strict_types=1);

namespace App\Domains\Authentication\ValueObjects;

use InvalidArgumentException;

final readonly class DeviceFingerprint
{
    public string $value;

    public function __construct(string $fingerprint)
    {
        $fingerprint = trim($fingerprint);

        if (strlen($fingerprint) < 32) {
            throw new InvalidArgumentException(
                'Invalid device fingerprint.'
            );
        }

        $this->value = $fingerprint;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}