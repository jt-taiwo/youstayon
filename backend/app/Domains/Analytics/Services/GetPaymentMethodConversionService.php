<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Services;

use App\Domains\Analytics\Contracts\GetPaymentMethodConversionServiceInterface;
use App\Domains\Analytics\DTOs\PaymentMethodConversionDTO;
use App\Domains\Purchase\Models\Purchase;

final class GetPaymentMethodConversionService
    implements GetPaymentMethodConversionServiceInterface
{
    public function execute(): PaymentMethodConversionDTO
    {
        $wallet = Purchase::query()
            ->where('payment_method', 'wallet')
            ->count();

        $payNow = Purchase::query()
            ->where('payment_method', 'pay_now')
            ->count();

        $total = $wallet + $payNow;

        return new PaymentMethodConversionDTO(
            walletPurchases: $wallet,
            payNowPurchases: $payNow,
            walletConversionRate: $total === 0
                ? 0
                : round(($wallet / $total) * 100, 2),
            payNowConversionRate: $total === 0
                ? 0
                : round(($payNow / $total) * 100, 2),
        );
    }
}
