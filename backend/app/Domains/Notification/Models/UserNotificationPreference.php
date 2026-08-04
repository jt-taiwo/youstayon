<?php

declare(strict_types=1);

namespace App\Domains\Notification\Models;

use App\Domains\User\Models\User;
use Database\Factories\UserNotificationPreferenceFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class UserNotificationPreference extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'user_id',
        'email_enabled',
        'push_enabled',
        'sms_enabled',
        'reminders_enabled',
        'radar_enabled',
        'quiet_hours_enabled',
        'quiet_hours_start',
        'quiet_hours_end',
    ];

    protected $casts = [
        'email_enabled' => 'boolean',
        'push_enabled' => 'boolean',
        'sms_enabled' => 'boolean',
        'reminders_enabled' => 'boolean',
        'radar_enabled' => 'boolean',
        'quiet_hours_enabled' => 'boolean',
        'quiet_hours_start' => 'datetime:H:i:s',
        'quiet_hours_end' => 'datetime:H:i:s',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): UserNotificationPreferenceFactory
    {
        return UserNotificationPreferenceFactory::new();
    }
}
