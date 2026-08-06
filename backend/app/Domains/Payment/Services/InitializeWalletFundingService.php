<?php

declare(strict_types=1);

namespace App\Domains\Payment\Services;

use App\Domains\Payment\Contracts\InitializeWalletFundingServiceInterface;
use App\Domains\Payment\Contracts\PaymentGatewayInterface;
use App\Domains\Payment\Contracts\PaymentTransactionRepositoryInterface;
use App\Domains\User\Models\User;
use Illuminate\Support\Str;

final readonly class InitializeWalletFundingService
    implements InitializeWalletFundingServiceInterface
{
    public function __construct(
        private PaymentGatewayInterface $gateway,
        private PaymentTransactionRepositoryInterface $payments
    ) {
    }

    public function execute(
        User $user,
        float $amount
    ): array {

        $reference = 'FUND-'
            . strtoupper(Str::random(12));

        $payment = $this->payments->create(
            $user,
            [
                'uuid' => (string) Str::uuid(),
                'provider' => config('payment.default_gateway'),
                'reference' => $reference,
                'amount' => $amount,
                'currency' => 'NGN',
                'status' => 'pending',
                'meta' => [],
            ]
        );

        $gateway = $this->gateway
            ->initializePayment(
                reference: $reference,
                amount: $amount,
                currency: 'NGN',
                email: $user->email,
                meta: [
                    'name' => trim(
                        $user->first_name
                        . ' '
                        . $user->last_name
                    ),
                ]
            );

        $payment->provider_reference =
            $gateway->providerReference;

        $this->payments->save($payment);

        return [
            'reference' => $reference,
            'checkout_url' =>
                $gateway->authorizationUrl,
        ];
    }
}
