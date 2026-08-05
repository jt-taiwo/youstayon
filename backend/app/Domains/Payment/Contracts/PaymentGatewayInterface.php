<?php

declare(strict_types=1);

namespace App\Domains\Payment\Contracts;

use App\Domains\Payment\DTOs\PaymentInitializationDTO;
use App\Domains\Payment\DTOs\PaymentVerificationDTO;

interface PaymentGatewayInterface
{
    public function initializePayment(
        string $reference,
        float $amount,
        string $currency,
        string $email,
        array $meta = []
    ): PaymentInitializationDTO;

    public function verifyPayment(
        string $reference
    ): PaymentVerificationDTO;
}
