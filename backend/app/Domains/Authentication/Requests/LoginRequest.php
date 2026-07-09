<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Requests;

final class LoginRequest
{
    public function rules(): array
    {
        return [

            'email'=>['required','email'],

            'password'=>['required'],

            'device_name'=>['nullable','string','max:255']

        ];
    }        
}