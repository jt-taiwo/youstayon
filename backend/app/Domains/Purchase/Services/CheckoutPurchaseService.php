<?php

declare(strict_types=1);

namespace App\Domains\Purchase\Services;

use App\Domains\Purchase\Contracts\CheckoutPurchaseServiceInterface;
use App\Domains\Purchase\Contracts\ExecuteWalletPurchaseServiceInterface;
use App\Domains\Purchase\Contracts\InitializePayNowPurchaseServiceInterface;
use App\Domains\User\Models\User;

final readonly class CheckoutPurchaseService
    implements CheckoutPurchaseServiceInterface
{
    public function __construct(
        private ExecuteWalletPurchaseServiceInterface $walletPurchases,
        private InitializePayNowPurchaseServiceInterface $payNow
    ) {
    }

    public function execute(
        User $user,
        string $serviceType,
        float $amount,
        string $paymentMethod,
        array $payload
    ): array {

        if ($paymentMethod === 'wallet') {

            $purchase = $this->walletPurchases->execute(
                user: $user,
                serviceType: $serviceType,
                amount: $amount,
                payload: $payload
            );

            return [
                'payment_method' => 'wallet',
                'status' => 'successful',
                'purchase_reference' => $purchase->reference,
                'purchase' => $purchase,
            ];
        }

        $payment = $this->payNow->execute(
            user: $user,
            serviceType: $serviceType,
            amount: $amount,
            payload: $payload
        );

        return [
            'payment_method' => 'pay_now',
            'status' => 'pending_payment',
            'purchase_reference' => $payment['reference'],
            'checkout_url' => $payment['checkout_url'],
        ];
    }
}
