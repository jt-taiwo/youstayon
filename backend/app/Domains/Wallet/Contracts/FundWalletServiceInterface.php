<?php

declare(strict_types=1);

namespace App\Domains\Wallet\Contracts;

use App\Domains\User\Models\User;
use App\Domains\Wallet\DTOs\WalletFundingResultDTO;

interface FundWalletServiceInterface
{
    public function execute(
        User $user,
        float $amount,
        string $reference,
        string $description = 'Wallet funding',
        array $meta = []
    ): WalletFundingResultDTO;
}
