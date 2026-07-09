<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Resources;

final class UserResource
{
    public function toArray($request): array
{
    return [

        'uuid'=>$this->uuid,

        'first_name'=>$this->first_name,

        'last_name'=>$this->last_name,

        'full_name'=>$this->full_name,

        'email'=>$this->email,

        'phone'=>$this->phone,

        'status'=>$this->status,

        'email_verified'=>$this->email_verified_at!==null,

        'created_at'=>$this->created_at,

    ];
}
}