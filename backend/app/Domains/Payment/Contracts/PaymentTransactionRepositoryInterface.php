<?php

declare(strict_types=1);

namespace App\Domains\Payment\Contracts;

use App\Domains\Payment\Models\PaymentTransaction;
use App\Domains\User\Models\User;

interface PaymentTransactionRepositoryInterface
{
    public function create(
        User $user,
        array $attributes
    ): PaymentTransaction;

    public function findByReference(
        string $reference
    ): ?PaymentTransaction;

    public function save(
        PaymentTransaction $transaction
    ): PaymentTransaction;
}
