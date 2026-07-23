<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class SubscriptionUsageRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'subscription_id',
        'quantity',
        'unit',
        'source',
        'recorded_at',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'decimal:4',
            'recorded_at' => 'datetime',
        ];
    }

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(
            Subscription::class
        );
    }
}