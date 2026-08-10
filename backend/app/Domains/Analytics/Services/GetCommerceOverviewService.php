<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Services;

use App\Domains\Analytics\Contracts\GetCommerceOverviewServiceInterface;
use App\Domains\Analytics\DTOs\CommerceOverviewDTO;
use App\Domains\Payment\Models\PaymentTransaction;
use App\Domains\Purchase\Models\Purchase;

final class GetCommerceOverviewService
    implements GetCommerceOverviewServiceInterface
{
    public function execute(): CommerceOverviewDTO
    {
        $purchases = Purchase::query();

        $successful = Purchase::query()
            ->where('status', 'successful');

        $walletFundings = PaymentTransaction::query()
            ->where('status', 'successful');

        $totalPurchaseVolume = (float) $successful
            ->sum('amount');

        /**
         * MVP revenue assumption:
         * 2.5% average margin across utility purchases.
         */
        $estimatedRevenue = round(
            $totalPurchaseVolume * 0.025,
            2
        );

        return new CommerceOverviewDTO(
            totalPurchases: $purchases->count(),

            totalPurchaseVolume: $totalPurchaseVolume,

            estimatedRevenue: $estimatedRevenue,

            walletFundings: $walletFundings->count(),

            walletFundingVolume: (float) $walletFundings
                ->sum('amount'),

            walletPurchases: Purchase::query()
                ->where('payment_method', 'wallet')
                ->count(),

            payNowPurchases: Purchase::query()
                ->where('payment_method', 'pay_now')
                ->count(),

            successfulPurchases: Purchase::query()
                ->where('status', 'successful')
                ->count(),

            failedPurchases: Purchase::query()
                ->where('status', 'failed')
                ->count(),
        );
    }
}
