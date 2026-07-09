<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Requests;

final class RegisterRequest
{
    public function authorize(): bool
{
    return true;
}

public function rules(): array
{
    return [

        'first_name'=>['required','string','max:100'],

        'last_name'=>['required','string','max:100'],

        'email'=>['required','email','max:255'],

        'phone'=>['required','string','max:20'],

        'password'=>[
            'required',
            'confirmed',
            'min:8',
            'max:100'
            ]
        ];
    }

}