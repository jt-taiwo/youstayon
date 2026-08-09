<?php

declare(strict_types=1);

namespace App\Domains\Purchase\Services;

use App\Domains\Purchase\Contracts\ExecuteWalletPurchaseServiceInterface;
use App\Domains\Purchase\Contracts\PurchaseRepositoryInterface;
use App\Domains\Purchase\Contracts\UtilityProviderInterface;
use App\Domains\Purchase\Models\Purchase;
use App\Domains\User\Models\User;
use App\Domains\Wallet\Contracts\DebitWalletServiceInterface;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final readonly class ExecuteWalletPurchaseService
    implements ExecuteWalletPurchaseServiceInterface
{
    public function __construct(
        private DebitWalletServiceInterface $wallets,
        private PurchaseRepositoryInterface $purchases,
        private UtilityProviderInterface $provider,
        private AutoRenewSubscriptionServiceInterface $renewals
    ) {
    }

    public function execute(
        User $user,
        string $serviceType,
        float $amount,
        array $payload
    ): Purchase {

        return DB::transaction(function () use (
            $user,
            $serviceType,
            $amount,
            $payload
        ): Purchase {

            $reference = 'PUR-'
                . strtoupper(Str::random(12));

            $this->wallets->execute(
                user: $user,
                amount: $amount,
                reference: $reference,
                description: ucfirst($serviceType) . ' purchase'
            );

            $purchase = $this->purchases->create(
                $user,
                [
                    'uuid' => (string) Str::uuid(),
                    'service_type' => $serviceType,
                    'provider' => 'internal',
                    'payment_method' => 'wallet',
                    'reference' => $reference,
                    'amount' => $amount,
                    'currency' => 'NGN',
                    'status' => 'processing',
                    'request_payload' => $payload,
                ]
            );

            $result = $this->provider->purchase(
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

            return $purchase;
        });
    }
}
