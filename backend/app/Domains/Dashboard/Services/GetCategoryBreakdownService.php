<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Services;

use App\Domains\Dashboard\Contracts\GetCategoryBreakdownServiceInterface;
use App\Domains\Dashboard\DTOs\CategoryBreakdownItemDTO;
use App\Domains\Subscription\Enums\SubscriptionStatus;
use App\Domains\Subscription\Models\Subscription;
use App\Domains\User\Models\User;

final class GetCategoryBreakdownService
    implements GetCategoryBreakdownServiceInterface
{
    public function execute(User $user): array
    {
        $rows = Subscription::query()
            ->selectRaw(
                'subscription_categories.name as category,
                 COUNT(subscriptions.id) as subscriptions,
                 SUM(subscriptions.amount) as monthly_spend'
            )
            ->join(
                'subscription_categories',
                'subscription_categories.id',
                '=',
                'subscriptions.subscription_category_id'
            )
            ->where(
                'subscriptions.user_id',
                $user->id
            )
            ->where(
                'subscriptions.status',
                SubscriptionStatus::ACTIVE
            )
            ->groupBy('subscription_categories.name')
            ->orderByDesc('monthly_spend')
            ->get();

        return $rows
            ->map(fn ($row) => new CategoryBreakdownItemDTO(
                category: $row->category,
                subscriptions: (int) $row->subscriptions,
                monthlySpend: (float) $row->monthly_spend,
            ))
            ->all();
    }
}
