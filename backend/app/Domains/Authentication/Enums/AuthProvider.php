<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Enums;

enum AuthProvider:string
{
    case LOCAL='local';
    case GOOGLE='google';
    case APPLE='apple';
    case FACEBOOK='facebook';
}