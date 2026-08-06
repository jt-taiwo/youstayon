<?php

declare(strict_types=1);

namespace App\Domains\Payment\Repositories;

use App\Domains\Payment\Contracts\PaymentTransactionRepositoryInterface;
use App\Domains\Payment\Models\PaymentTransaction;
use App\Domains\User\Models\User;

final class PaymentTransactionRepository
    implements PaymentTransactionRepositoryInterface
{
    public function create(
        User $user,
        array $attributes
    ): PaymentTransaction {

        return PaymentTransaction::query()->create([
            'user_id' => $user->id,
            ...$attributes,
        ]);
    }

    public function findByReference(
        string $reference
    ): ?PaymentTransaction {

        return PaymentTransaction::query()
            ->where(
                'reference',
                $reference
            )
            ->first();
    }

    public function save(
        PaymentTransaction $transaction
    ): PaymentTransaction {

        $transaction->save();

        return $transaction->refresh();
    }
}
