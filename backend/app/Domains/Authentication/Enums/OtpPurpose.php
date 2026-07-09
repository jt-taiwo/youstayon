<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Enums;

enum OtpPurpose:string
{
    case REGISTRATION='registration';
    case LOGIN='login';
    case PASSWORD_RESET='password_reset';
    case EMAIL_VERIFICATION='email_verification';
}