<?php

declare(strict_types=1);

namespace App\Domains\User\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class UpdateAvatarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [

            'avatar' => [

                'required',

                'image',

                'mimes:jpeg,jpg,png,webp',

                'max:2048',

                'dimensions:min_width=100,min_height=100,max_width=4096,max_height=4096',

            ],

        ];
    }
}