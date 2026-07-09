<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Requests;

final class ResetPasswordRequest
{
    public function rules(): array
{
    return [

        'token'=>['required'],

        'email'=>['required','email'],

        'password'=>[
            'required',
            'confirmed',
            'min:8'
        ]

    ];
}
}