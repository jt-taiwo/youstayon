<?php

declare(strict_types=1);

namespace App\Domains\Payment\DTOs;

final readonly class PaymentInitializationDTO
{
    public function __construct(
        public string $reference,
        public string $provider,
        public string $authorizationUrl,
        public string $providerReference,
    ) {
    }
}
