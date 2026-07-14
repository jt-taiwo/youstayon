<?php

declare(strict_types=1);

namespace App\Domains\User\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Core\Traits\UsesUuid;
use Database\Factories\UserFactory;

class User extends Authenticatable
{
    use HasFactory;
    use HasApiTokens;
    use Notifiable;
    use UsesUuid;

    protected $fillable = [

        'uuid',

        'first_name',

        'last_name',

        'email',

        'phone',

        'password',

        'status',

        'email_verified_at',

    ];

    protected $hidden = [

        'password',

        'remember_token',

    ];

    protected function casts(): array
    {
        return [

            'email_verified_at'=>'datetime',

            'password'=>'hashed',

        ];
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}