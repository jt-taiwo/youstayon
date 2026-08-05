<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Enums;

enum RenewalSuggestion: string
{
    case RENEW_NOW = 'renew_now';
    case RENEW_SOON = 'renew_soon';
    case WAIT = 'wait';
    case NO_ACTION = 'no_action';
}
