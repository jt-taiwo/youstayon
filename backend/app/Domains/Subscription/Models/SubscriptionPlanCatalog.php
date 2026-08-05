<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Models;

use Database\Factories\SubscriptionPlanCatalogFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class SubscriptionPlanCatalog extends Model
{
    use HasFactory;

    protected $table = 'subscription_plan_catalog';

    protected $fillable = [
        'uuid',
        'subscription_category_id',
        'provider_name',
        'plan_name',
        'amount',
        'usage_limit',
        'usage_unit',
        'currency',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'usage_limit' => 'decimal:4',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(
            SubscriptionCategory::class,
            'subscription_category_id'
        );
    }

    protected static function newFactory(): SubscriptionPlanCatalogFactory
    {
        return SubscriptionPlanCatalogFactory::new();
    }
}
