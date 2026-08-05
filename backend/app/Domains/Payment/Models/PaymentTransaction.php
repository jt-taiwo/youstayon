<?php

declare(strict_types=1);

namespace App\Domains\Payment\Models;

use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'provider',
        'provider_reference',
        'reference',
        'amount',
        'currency',
        'status',
        'meta',
        'paid_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'meta' => 'array',
        'paid_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
