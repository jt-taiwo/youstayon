<?php

declare(strict_types=1);

namespace App\Domains\Authentication\Resources;

final class AuthenticationResource
{
    return [

    'token'=>$this['token'],

    'token_type'=>'Bearer',

    'user'=>new UserResource($this['user'])

];
}