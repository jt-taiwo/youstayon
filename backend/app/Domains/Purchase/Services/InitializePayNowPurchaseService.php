<?php

declare(strict_types=1);

namespace App\Domains\Purchase\Services;

use App\Domains\Payment\Contracts\PaymentGatewayInterface;
use App\Domains\Payment\Contracts\PaymentTransactionRepositoryInterface;
use App\Domains\Purchase\Contracts\InitializePayNowPurchaseServiceInterface;
use App\Domains\Purchase\Contracts\PurchaseRepositoryInterface;
use App\Domains\User\Models\User;
use Illuminate\Support\Str;

final readonly class InitializePayNowPurchaseService
    implements InitializePayNowPurchaseServiceInterface
{
    public function __construct(
        private PaymentGatewayInterface $gateway,
        private PaymentTransactionRepositoryInterface $payments,
        private PurchaseRepositoryInterface $purchases
    ) {
    }

    public function execute(
        User $user,
        string $serviceType,
        float $amount,
        array $payload
    ): array {

        $reference = 'PUR-'
            . strtoupper(Str::random(12));

        $this->purchases->create(
            $user,
            [
                'uuid' => (string) Str::uuid(),
                'service_type' => $serviceType,
                'provider' => config('payment.default_gateway'),
                'payment_method' => 'pay_now',
                'reference' => $reference,
                'amount' => $amount,
                'currency' => 'NGN',
                'status' => 'pending',
                'request_payload' => $payload,
            ]
        );

        $payment = $this->payments->create(
            $user,
            [
                'uuid' => (string) Str::uuid(),
                'provider' => config('payment.default_gateway'),
                'reference' => $reference,
                'amount' => $amount,
                'currency' => 'NGN',
                'status' => 'pending',
                'meta' => [
                    'service_type' => $serviceType,
                ],
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
