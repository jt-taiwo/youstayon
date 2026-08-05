<?php

declare(strict_types=1);

namespace App\Domains\Payment\DTOs;

final readonly class PaymentVerificationDTO
{
    public function __construct(
        public string $reference,
        public string $provider,
        public bool $successful,
        public float $amount,
        public string $currency,
        public string $providerReference,
        public array $meta = [],
    ) {
    }
}
