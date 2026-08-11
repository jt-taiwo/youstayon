<?php

declare(strict_types=1);

namespace App\Domains\Analytics\DTOs;

final readonly class PaymentMethodConversionDTO
{
    public function __construct(
        public int $walletPurchases,
        public int $payNowPurchases,
        public float $walletConversionRate,
        public float $payNowConversionRate,
    ) {
    }
}
