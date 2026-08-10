<?php

declare(strict_types=1);

namespace App\Domains\Analytics\Services;

use App\Domains\Analytics\Contracts\GetRenewalRadarAnalyticsServiceInterface;
use App\Domains\Analytics\DTOs\RenewalRadarAnalyticsDTO;
use App\Domains\Purchase\Models\Purchase;
use App\Domains\Subscription\Models\Subscription;

final class GetRenewalRadarAnalyticsService
    implements GetRenewalRadarAnalyticsServiceInterface
{
    public function execute(): RenewalRadarAnalyticsDTO
    {
        $expiring = Subscription::query()
            ->whereDate('expires_at', '<=', now()->addDays(7))
            ->count();

        $renewed = Purchase::query()
            ->where('status', 'successful')
            ->whereNotNull('subscription_id')
            ->count();

        $conversion = $expiring === 0
            ? 0
            : round(
                ($renewed / $expiring) * 100,
                2
            );

        $radarPurchases = Purchase::query()
            ->where('attribution_source', 'radar')
            ->where('status', 'successful');

        $notificationPurchases = Purchase::query()
            ->where('attribution_source', 'notification')
            ->where('status', 'successful');

        return new RenewalRadarAnalyticsDTO(
            expiringSubscriptions: $expiring,

            renewedSubscriptions: $renewed,

            renewalConversionRate: $conversion,

            radarAttributedPurchases:
                $radarPurchases->count(),

            notificationAttributedPurchases:
                $notificationPurchases->count(),

            radarRevenue: round(
                (float) $radarPurchases->sum('amount')
                * 0.025,
                2
            ),
        );
    }
}
