<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Requests;

final class ForgotPasswordRequest
{
            public function rules(): array
{
    return [

        'email'=>['required','email']

    ];
}
}