<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Enums;

enum AccountStatus:string
{
    case ACTIVE='active';
    case PENDING='pending';
    case SUSPENDED='suspended';
    case BLOCKED='blocked';
    case DELETED='deleted';
}