<?php

declare(strict_types=1);

namespace App\Domains\Authentication\ValueObjects;

use InvalidArgumentException;

final readonly class OtpCode
{
    public string $value;

    public function __construct(string $otp)
    {
        if (! preg_match('/^[0-9]{6}$/', $otp)) {
            throw new InvalidArgumentException('OTP must contain exactly six digits.');
        }

        $this->value = $otp;
    }

    public static function generate(): self
    {
        return new self(
            str_pad(
                (string) random_int(0, 999999),
                6,
                '0',
                STR_PAD_LEFT
            )
        );
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