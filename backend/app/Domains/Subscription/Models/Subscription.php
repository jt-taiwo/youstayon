<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Models;

use App\Domains\Subscription\Enums\SubscriptionStatus;
use App\Domains\Subscription\Exceptions\SubscriptionCannotBeCancelledException;
use App\Domains\User\Models\User;
use Database\Factories\SubscriptionFactory;
use DomainException;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

final class Subscription extends Model
{
    use HasFactory;

    protected static function newFactory(): SubscriptionFactory
    {
        return SubscriptionFactory::new();
    }

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
            'status' => SubscriptionStatus::class,
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $subscription): void {
            $subscription->uuid ??= (string) Str::uuid();
        });
    }

    //Subscription cCncellation method
    // cancellation does not mean reversing a completed recharge. 
    // It means cancelling an active subscription record that is still eligible 
    // for cancellation.
    public function cancel(): void
    {
        if (! $this->status->canBeCancelled()) {
            throw new SubscriptionCannotBeCancelledException();
        }

        $this->status = SubscriptionStatus::CANCELLED;
    }

    // Relationships
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