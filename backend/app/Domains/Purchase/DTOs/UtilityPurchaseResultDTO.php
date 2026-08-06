<?php

declare(strict_types=1);

namespace App\Domains\Purchase\DTOs;

final readonly class UtilityPurchaseResultDTO
{
    public function __construct(
        public bool $successful,
        public string $providerReference,
        public array $response,
    ) {
    }
}
