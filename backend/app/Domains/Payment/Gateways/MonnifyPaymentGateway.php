<?php

declare(strict_types=1);

namespace App\Domains\Payment\Gateways;

use App\Domains\Payment\Contracts\PaymentGatewayInterface;
use App\Domains\Payment\DTOs\PaymentInitializationDTO;
use App\Domains\Payment\DTOs\PaymentVerificationDTO;
use Illuminate\Support\Facades\Http;

final readonly class MonnifyPaymentGateway
    implements PaymentGatewayInterface
{
    public function __construct(
        private MonnifyTokenService $tokens
    ) {
    }

    public function initializePayment(
        string $reference,
        float $amount,
        string $currency,
        string $email,
        array $meta = []
    ): PaymentInitializationDTO {

        $token = $this->tokens
            ->getAccessToken();

        $response = Http::withToken($token)
            ->post(
                config('payment.monnify.base_url')
                . '/api/v1/merchant/transactions/init-transaction',
                [
                    'amount' => $amount,
                    'customerName' => $meta['name']
                        ?? 'YouStayOn User',
                    'customerEmail' => $email,
                    'paymentReference' => $reference,
                    'paymentDescription' => 'Wallet Funding',
                    'currencyCode' => $currency,
                    'contractCode' => config(
                        'payment.monnify.contract_code'
                    ),
                    'redirectUrl' => config(
                        'payment.monnify.redirect_url'
                    ),
                ]
            );

        $response->throw();

        $body = $response
            ->json('responseBody');

        return new PaymentInitializationDTO(
            reference: $reference,
            provider: 'monnify',
            authorizationUrl: $body['checkoutUrl'],
            providerReference: $body['transactionReference'],
        );
    }

    public function verifyPayment(
        string $reference
    ): PaymentVerificationDTO {

        $token = $this->tokens
            ->getAccessToken();

        $response = Http::withToken($token)
            ->get(
                config('payment.monnify.base_url')
                . '/api/v2/transactions/'
                . $reference
            );

        $response->throw();

        $body = $response
            ->json('responseBody');

        return new PaymentVerificationDTO(
            reference: $reference,
            provider: 'monnify',
            successful: ($body['paymentStatus']
                ?? '') === 'PAID',
            amount: (float) $body['amountPaid'],
            currency: $body['currency']
                ?? 'NGN',
            providerReference: $body['transactionReference'],
            meta: $body,
        );
    }
}
