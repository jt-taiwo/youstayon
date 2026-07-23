<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CreateSubscriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_uuid' => [
                'required',
                'uuid',
            ],

            'provider_name' => [
                'required',
                'string',
                'max:255',
            ],

            'plan_name' => [
                'nullable',
                'string',
                'max:255',
            ],

            'amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'usage_limit' => [
                'nullable',
                'numeric',
                'gt:0',
                'required_with:usage_unit',
            ],

            'usage_unit' => [
                'nullable',
                'string',
                'max:20',
                'required_with:usage_limit',
            ],

            'currency' => [
                'sometimes',
                'string',
                'size:3',
            ],

            'started_at' => [
                'nullable',
                'date',
            ],

            'expires_at' => [
                'nullable',
                'date',
                'after:started_at',
            ],

            'renewal_at' => [
                'nullable',
                'date',
                'after_or_equal:expires_at',
            ],

            'notes' => [
                'nullable',
                'string',
            ],
        ];
    }
}