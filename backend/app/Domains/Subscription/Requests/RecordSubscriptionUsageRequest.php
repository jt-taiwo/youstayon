<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class RecordSubscriptionUsageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'quantity' => [
                'required',
                'numeric',
                'gt:0',
            ],

            'unit' => [
                'required',
                'string',
                'max:20',
            ],

            'source' => [
                'sometimes',
                'string',
                'max:50',
            ],

            'recorded_at' => [
                'sometimes',
                'date',
            ],
        ];
    }
}