<?php

declare(strict_types=1);

namespace App\Domains\Payment\Services;

use App\Domains\Payment\Contracts\PaymentGatewayInterface;
use App\Domains\Payment\Contracts\PaymentTransactionRepositoryInterface;
use App\Domains\Payment\Contracts\VerifyWalletFundingServiceInterface;
use App\Domains\Wallet\Contracts\FundWalletServiceInterface;
use Illuminate\Support\Carbon;

final readonly class VerifyWalletFundingService
    implements VerifyWalletFundingServiceInterface
{
    public function __construct(
        private PaymentGatewayInterface $gateway,
        private PaymentTransactionRepositoryInterface $payments,
        private FundWalletServiceInterface $wallets
    ) {
    }

    public function execute(
        string $reference
    ): bool {

        $payment = $this->payments
            ->findByReference($reference);

        if ($payment === null) {
            return false;
        }

        if ($payment->status === 'successful') {
            return true;
        }

        $verification = $this->gateway
            ->verifyPayment($reference);

        if (! $verification->successful) {
            $payment->status = 'failed';

            $this->payments->save($payment);

            return false;
        }

        $this->wallets->execute(
            user: $payment->user,
            amount: (float) $payment->amount,
            reference: $payment->reference,
            description: 'Wallet funding'
        );

        $payment->status = 'successful';
        $payment->paid_at = Carbon::now();

        $this->payments->save($payment);

        return true;
    }
}
