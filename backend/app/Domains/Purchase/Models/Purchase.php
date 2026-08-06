<?php

declare(strict_types=1);

namespace App\Domains\Purchase\Models;

use App\Domains\User\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'service_type',
        'provider',
        'provider_reference',
        'payment_method',
        'reference',
        'amount',
        'currency',
        'status',
        'request_payload',
        'response_payload',
        'completed_at',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'request_payload' => 'array',
        'response_payload' => 'array',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
