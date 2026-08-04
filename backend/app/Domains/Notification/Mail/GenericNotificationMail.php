<?php

declare(strict_types=1);

namespace App\Domains\Notification\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

final class GenericNotificationMail extends Mailable
{
    use Queueable;
    use SerializesModels;

    public string $notificationMessage;

    public function __construct(
        string $subject,
        string $message,
    ) {
        $this->subject = $subject;
        $this->notificationMessage = $message;
    }

    public function build(): self
    {
        return $this
            ->subject($this->subject)
            ->text('emails.notifications.generic');
    }
}
