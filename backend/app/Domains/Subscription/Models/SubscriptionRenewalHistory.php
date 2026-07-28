<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

final class SubscriptionRenewalHistory extends Model
{
    use HasFactory;
    
    protected static function newFactory()
    {
        return \Database\Factories\SubscriptionRenewalHistoryFactory::new();
    }

    protected $fillable = [
        'uuid',
        'user_id',
        'previous_subscription_id',
        'new_subscription_id',
        'previous_start_date',
        'previous_expiry_date',
        'new_start_date',
        'new_expiry_date',
        'reason',
        'metadata',
        'renewed_at',
    ];

    protected $casts = [
        'previous_start_date' => 'date',
        'previous_expiry_date' => 'date',
        'new_start_date' => 'date',
        'new_expiry_date' => 'date',
        'renewed_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (self $history): void {
            if (blank($history->uuid)) {
                $history->uuid = (string) Str::uuid();
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function previousSubscription(): BelongsTo
    {
        return $this->belongsTo(
            Subscription::class,
            'previous_subscription_id'
        );
    }

    public function newSubscription(): BelongsTo
    {
        return $this->belongsTo(
            Subscription::class,
            'new_subscription_id'
        );
    }
}