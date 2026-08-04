<?php

declare(strict_types=1);

namespace App\Domains\Notification\Channels;

use App\Domains\Notification\Mail\GenericNotificationMail;
use App\Domains\Notification\Models\Notification;
use Illuminate\Support\Facades\Mail;

final class EmailNotificationChannel
{
    public function send(Notification $notification): void
    {
        $user = $notification->user;

        if ($user === null || empty($user->email)) {
            return;
        }

        Mail::to($user->email)->send(
            new GenericNotificationMail(
                subject: $notification->title,
                message: $notification->message,
            )
        );
    }
}
