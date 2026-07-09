<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Requests;

final class VerifyOtpRequest
    {
    public function rules(): array
    {
        return [

            'refresh_token'=>['required','string']

        ];
    }
}