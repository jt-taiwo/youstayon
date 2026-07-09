<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Base\Models\BaseModel;
use App\Core\Traits\UsesUuid;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use UsesUuid;
    use MustVerifyEmail;

    /**
     * @var array<int,string>
     */
    protected $fillable = [
        'uuid',
        'first_name',
        'last_name',
        'email',
        'phone',
        'password',
        'status',
    ];

    /**
     * @var array<int,string>
     */
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

    /**
     * Public identifier.
     */
    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    /**
     * Display name.
     */
    public function getFullNameAttribute(): string
    {
        return trim(
            "{$this->first_name} {$this->last_name}"
        );
    }
}

/*
|--------------------------------------------------------------------------
| Relationships
|--------------------------------------------------------------------------
|
| Subscription Module
| Purchase Module
| Notification Module
| Security Module
| Wallet Module
|
*/