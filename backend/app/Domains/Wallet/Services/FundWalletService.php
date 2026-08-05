<?php

declare(strict_types=1);

namespace App\Domains\Wallet\Services;

use App\Domains\User\Models\User;
use App\Domains\Wallet\Contracts\FundWalletServiceInterface;
use App\Domains\Wallet\Contracts\WalletRepositoryInterface;
use App\Domains\Wallet\DTOs\WalletFundingResultDTO;
use App\Domains\Wallet\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

final readonly class FundWalletService
    implements FundWalletServiceInterface
{
    public function __construct(
        private WalletRepositoryInterface $wallets
    ) {
    }

    public function execute(
        User $user,
        float $amount,
        string $reference,
        string $description = 'Wallet funding',
        array $meta = []
    ): WalletFundingResultDTO {
        return DB::transaction(function () use (
            $user,
            $amount,
            $reference,
            $description,
            $meta
        ): WalletFundingResultDTO {

            $wallet = $this->wallets
                ->getOrCreateForUser($user);

            $before = (float) $wallet->balance;

            $after = $before + $amount;

            $wallet->balance = $after;

            $wallet = $this->wallets->save($wallet);

            $transaction = WalletTransaction::query()->create([
                'uuid' => (string) Str::uuid(),
                'wallet_id' => $wallet->id,
                'type' => 'credit',
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'reference' => $reference,
                'description' => $description,
                'meta' => $meta,
            ]);

            return new WalletFundingResultDTO(
                walletUuid: $wallet->uuid,
                transactionUuid: $transaction->uuid,
                amount: $amount,
                newBalance: $after,
                reference: $reference,
            );
        });
    }
}
