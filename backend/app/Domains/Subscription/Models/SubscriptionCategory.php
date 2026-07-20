<?php

declare(strict_types=1);

namespace App\Domains\Subscription\Models;

use Database\Factories\SubscriptionCategoryFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

final class SubscriptionCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (self $category): void {
            $category->uuid ??= (string) Str::uuid();
        });
    }

    protected static function newFactory(): SubscriptionCategoryFactory
    {
        return SubscriptionCategoryFactory::new();
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(
            Subscription::class,
            'subscription_category_id'
        );
    }
}