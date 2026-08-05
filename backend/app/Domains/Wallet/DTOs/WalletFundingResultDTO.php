<?php

declare(strict_types=1);

namespace App\Domains\Wallet\DTOs;

final readonly class WalletFundingResultDTO
{
    public function __construct(
        public string $walletUuid,
        public string $transactionUuid,
        public float $amount,
        public float $newBalance,
        public string $reference,
    ) {
    }
}
