<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

final class AuthenticationResource extends JsonResource
{
    /**
     * Transform the authentication response.
     */
    public function toArray(Request $request): array
    {
        return [

            'token' => $this['token'],

            'token_type' => $this['token_type'] ?? 'Bearer',

            'user' => $this['user'],

        ];
    }
}