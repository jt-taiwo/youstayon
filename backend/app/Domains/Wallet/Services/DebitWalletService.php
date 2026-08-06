<?php

declare(strict_types=1);

namespace App\Domains\Wallet\Services;

use App\Domains\User\Models\User;
use App\Domains\Wallet\Contracts\DebitWalletServiceInterface;
use App\Domains\Wallet\Contracts\WalletRepositoryInterface;
use App\Domains\Wallet\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use RuntimeException;
use Illuminate\Support\Str;

final readonly class DebitWalletService
    implements DebitWalletServiceInterface
{
    public function __construct(
        private WalletRepositoryInterface $wallets
    ) {
    }

    public function execute(
        User $user,
        float $amount,
        string $reference,
        string $description = 'Wallet debit',
        array $meta = []
    ): float {

        return DB::transaction(function () use (
            $user,
            $amount,
            $reference,
            $description,
            $meta
        ): float {

            $wallet = $this->wallets
                ->getOrCreateForUser($user);

            $before = (float) $wallet->balance;

            if ($before < $amount) {
                throw new RuntimeException(
                    'Insufficient wallet balance.'
                );
            }

            $after = $before - $amount;

            $wallet->balance = $after;

            $wallet = $this->wallets->save($wallet);

            WalletTransaction::query()->create([
                'uuid' => (string) Str::uuid(),
                'wallet_id' => $wallet->id,
                'type' => 'debit',
                'amount' => $amount,
                'balance_before' => $before,
                'balance_after' => $after,
                'reference' => $reference,
                'description' => $description,
                'meta' => $meta,
            ]);

            return $after;
        });
    }
}
