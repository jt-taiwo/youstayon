<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Enums;

enum SubscriptionRecommendation: string
{
    case RENEW_NOW = 'renew_now';
    case BUY_DATA_SOON = 'buy_data_soon';
    case MONITOR = 'monitor';
    case NO_ACTION_NEEDED = 'no_action_needed';
}
