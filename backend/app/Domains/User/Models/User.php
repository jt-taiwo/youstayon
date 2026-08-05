<?php

declare(strict_types=1);

namespace App\Domains\User\Models;

use App\Core\Traits\UsesUuid;
use App\Domains\Wallet\Models\Wallet;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Domains\Subscription\Models\Subscription;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use UsesUuid;

    protected $fillable = [

        'uuid',

        'first_name',

        'last_name',

        'email',

        'phone',

        'avatar',

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

            'email_verified_at' => 'datetime',

            'password' => 'hashed',

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

    // Relationship
    public function subscriptions(): HasMany
    {
        return $this->hasMany(
            Subscription::class
        );
    }

    public function wallet(): HasOne
    {
        return $this->hasOne(Wallet::class);
    }

    
}