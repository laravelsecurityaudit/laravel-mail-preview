<?php

namespace LaravelSecurityAudit\MailPreview\Tests\Feature;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;
use LaravelSecurityAudit\MailPreview\Models\MailPreviewEmail;
use LaravelSecurityAudit\MailPreview\Tests\Fixtures\MailNotifiable;
use LaravelSecurityAudit\MailPreview\Tests\Fixtures\VerificationNotification;
use LaravelSecurityAudit\MailPreview\Tests\TestCase;

class MailPreviewTest extends TestCase
{
    public function test_raw_mail_is_captured(): void
    {
        $this->runPackageMigrations();

        Mail::raw('Preview body text', function ($message): void {
            $message->to('reader@example.com')->subject('Preview subject');
        });

        $email = MailPreviewEmail::query()->first();

        $this->assertNotNull($email);
        $this->assertSame('array', $email->mailer);
        $this->assertSame('Preview subject', $email->subject);
        $this->assertSame('reader@example.com', $email->to_addresses[0]['address']);
        $this->assertStringContainsString('Preview body text', $email->text_body);
    }

    public function test_notification_mail_is_captured(): void
    {
        $this->runPackageMigrations();

        (new MailNotifiable)->notify(new VerificationNotification);

        $email = MailPreviewEmail::query()->first();

        $this->assertNotNull($email);
        $this->assertSame('reader@example.com', $email->to_addresses[0]['address']);
        $this->assertStringContainsString('Verify your email address', $email->subject);
        $this->assertStringContainsString('Verify Email Address', $email->html_body);
    }

    public function test_list_route_is_public_and_renders_captured_emails(): void
    {
        $this->runPackageMigrations();

        MailPreviewEmail::query()->create([
            'mailer' => 'array',
            'subject' => 'Verify your email address',
            'sender' => 'Mail Preview Sender <sender@example.com>',
            'recipients' => 'reader@example.com',
            'html_body' => '<p>Full email body</p>',
            'text_body' => 'Full email body',
            'headers' => 'Subject: Verify your email address',
            'attachments' => [],
            'captured_at' => now(),
        ]);

        $this->get(route('mail-preview.index'))
            ->assertOk()
            ->assertSee('Mail Preview')
            ->assertSee('reader@example.com')
            ->assertSee('Verify your email address');
    }

    public function test_detail_route_returns_full_email_payload(): void
    {
        $this->runPackageMigrations();

        $email = MailPreviewEmail::query()->create([
            'mailer' => 'array',
            'subject' => 'Purchase receipt',
            'sender' => 'sender@example.com',
            'recipients' => 'buyer@example.com',
            'html_body' => '<p>Your publication is ready.</p>',
            'text_body' => 'Your publication is ready.',
            'headers' => 'Subject: Purchase receipt',
            'attachments' => [
                [
                    'filename' => 'receipt.pdf',
                    'content_type' => 'application/pdf',
                    'disposition' => 'attachment',
                    'content_id' => null,
                ],
            ],
            'captured_at' => now(),
        ]);

        $this->getJson(route('mail-preview.show', $email))
            ->assertOk()
            ->assertJsonPath('subject', 'Purchase receipt')
            ->assertJsonPath('recipients', 'buyer@example.com')
            ->assertJsonPath('html_body', '<p>Your publication is ready.</p>')
            ->assertJsonPath('attachments.0.filename', 'receipt.pdf')
            ->assertJsonPath('delete_url', route('mail-preview.destroy', $email));
    }

    public function test_captured_email_can_be_deleted(): void
    {
        $this->runPackageMigrations();

        $email = MailPreviewEmail::query()->create([
            'mailer' => 'array',
            'subject' => 'Delete me',
            'recipients' => 'reader@example.com',
            'captured_at' => now(),
        ]);

        $this->delete(route('mail-preview.destroy', $email))
            ->assertRedirect(route('mail-preview.index'));

        $this->assertDatabaseMissing('mail_preview_emails', [
            'id' => $email->id,
        ]);
    }

    public function test_guarded_migration_no_ops_when_configured_table_already_exists(): void
    {
        config(['mail-preview.table' => 'existing_emails']);

        Schema::create('existing_emails', function ($table): void {
            $table->id();
            $table->string('existing_marker')->nullable();
        });

        $this->runPackageMigrations();

        $this->assertTrue(Schema::hasColumn('existing_emails', 'existing_marker'));
        $this->assertFalse(Schema::hasColumn('existing_emails', 'mailer'));
    }
}
