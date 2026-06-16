<?php

namespace LaravelSecurityAudit\MailPreview\Tests\Fixtures;

use Illuminate\Notifications\Notifiable;

class MailNotifiable
{
    use Notifiable;

    public string $email = 'reader@example.com';

    public function routeNotificationForMail(): string
    {
        return $this->email;
    }
}
