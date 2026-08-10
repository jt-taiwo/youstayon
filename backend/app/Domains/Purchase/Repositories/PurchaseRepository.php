<?php

declare(strict_types=1);

namespace App\Domains\Purchase\Repositories;

use App\Domains\Purchase\Contracts\PurchaseRepositoryInterface;
use App\Domains\Purchase\Models\Purchase;
use App\Domains\User\Models\User;

final class PurchaseRepository
    implements PurchaseRepositoryInterface
{
    public function create(
        User $user,
        array $attributes
    ): Purchase {

        return Purchase::query()->create([
            'user_id' => $user->id,
            ...$attributes,
        ]);
    }

    public function save(
        Purchase $purchase
    ): Purchase {

        $purchase->save();

        return $purchase->refresh();
    }

    public function findByReference(string $reference): ?Purchase
    {
        return Purchase::query()
            ->where('reference', $reference)
            ->first();
    }

}
