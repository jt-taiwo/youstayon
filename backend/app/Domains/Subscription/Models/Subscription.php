<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Models;

use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

final class Subscription extends Model
{
    protected $fillable = [
        'uuid',
        'user_id',
        'subscription_category_id',
        'provider_name',
        'plan_name',
        'amount',
        'currency',
        'started_at',
        'expires_at',
        'renewal_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'started_at' => 'datetime',
            'expires_at' => 'datetime',
            'renewal_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $subscription): void {
            $subscription->uuid ??= (string) Str::uuid();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(
            User::class
        );
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            SubscriptionCategory::class,
            'subscription_category_id'
        );
    }
}