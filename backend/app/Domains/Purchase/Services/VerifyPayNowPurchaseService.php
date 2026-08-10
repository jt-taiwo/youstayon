<?php

declare(strict_types=1);

namespace App\Domains\Purchase\Services;

use App\Domains\Payment\Contracts\PaymentGatewayInterface;
use App\Domains\Payment\Contracts\PaymentTransactionRepositoryInterface;
use App\Domains\Purchase\Contracts\PurchaseRepositoryInterface;
use App\Domains\Purchase\Contracts\UtilityProviderInterface;
use App\Domains\Purchase\Contracts\VerifyPayNowPurchaseServiceInterface;
use Illuminate\Support\Carbon;

final readonly class VerifyPayNowPurchaseService
    implements VerifyPayNowPurchaseServiceInterface
{
    public function __construct(
        private PaymentGatewayInterface $gateway,
        private PaymentTransactionRepositoryInterface $payments,
        private PurchaseRepositoryInterface $purchases,
        private UtilityProviderInterface $provider,
        private AutoRenewSubscriptionServiceInterface $renewals
    ) {
    }

    public function execute(
        string $reference
    ): bool {

        $payment = $this->payments->findByReference($reference);

        if ($payment === null) {
            return false;
        }

        if ($payment->status === 'successful') {
            return true;
        }

        $verification = $this->gateway->verifyPayment($reference);

        if (! $verification->successful) {
            $payment->status = 'failed';

            $this->payments->save($payment);

            return false;
        }

        $purchase = \App\Domains\Purchase\Models\Purchase::query()
            ->where('reference', $reference)
            ->first();

        if ($purchase === null) {
            return false;
        }

         $provider = $this->providerManager->current();

         $result = $provider->purchase(
        
            serviceType: $serviceType,
            amount: $amount,
            payload: $payload
        );

        $purchase->provider_reference = $result->providerReference;
        $purchase->status = $result->successful ? 'successful' : 'failed';
        $purchase->response_payload = $result->response;
        $purchase->completed_at = Carbon::now();

        $purchase = $this->purchases->save($purchase);

        if (
            $purchase->subscription !== null &&
            $purchase->status === 'successful'
        ) {
            $this->renewals->execute(
                $purchase->subscription,
                $purchase
            );
        }

        $payment->status = $result->successful ? 'successful' : 'failed';
        $payment->paid_at = Carbon::now();

        $this->payments->save($payment);

        return $result->successful;
    }
}
