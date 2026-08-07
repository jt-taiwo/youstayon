<?php

declare(strict_types=1);

namespace App\Domains\Purchase\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'service_type' => [
                'required',
                'string',
                'in:airtime,data,electricity,cable,internet',
            ],

            'amount' => [
                'required',
                'numeric',
                'min:50',
            ],

            'payment_method' => [
                'required',
                'string',
                'in:wallet,pay_now',
            ],

            'payload' => [
                'required',
                'array',
            ],
        ];
    }
}
