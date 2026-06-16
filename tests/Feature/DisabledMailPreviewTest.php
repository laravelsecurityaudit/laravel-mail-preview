<?php

namespace LaravelSecurityAudit\MailPreview\Tests\Feature;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use LaravelSecurityAudit\MailPreview\Tests\TestCase;

class DisabledMailPreviewTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('mail-preview.enabled', false);
    }

    public function test_disabled_package_does_not_register_routes_or_capture_messages(): void
    {
        $this->get('/_testing/emails')->assertNotFound();

        Mail::raw('This should not be captured.', function ($message): void {
            $message->to('reader@example.com')->subject('Disabled preview');
        });

        $this->assertFalse(Schema::hasTable('mail_preview_emails'));
    }
}
