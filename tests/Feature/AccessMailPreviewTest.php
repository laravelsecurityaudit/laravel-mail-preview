<?php

namespace LaravelSecurityAudit\MailPreview\Tests\Feature;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use LaravelSecurityAudit\MailPreview\Models\MailPreviewEmail;
use LaravelSecurityAudit\MailPreview\Tests\TestCase;

class AccessMailPreviewTest extends TestCase
{
    protected function defineEnvironment($app): void
    {
        parent::defineEnvironment($app);

        $app['config']->set('mail-preview.gate', 'viewMailPreview');
    }

    public function test_configured_gate_blocks_preview_access(): void
    {
        $this->runPackageMigrations();

        Gate::define('viewMailPreview', fn (): bool => false);

        $email = MailPreviewEmail::query()->create([
            'mailer' => 'array',
            'subject' => 'Blocked preview',
            'recipients' => 'reader@example.com',
            'captured_at' => now(),
        ]);

        $this->get(route('mail-preview.index'))->assertForbidden();
        $this->delete(route('mail-preview.destroy', $email))->assertForbidden();

        $this->assertDatabaseHas('mail_preview_emails', [
            'id' => $email->id,
        ]);
    }

    public function test_configured_gate_allows_preview_access(): void
    {
        $this->runPackageMigrations();

        $user = new class implements Authenticatable
        {
            public function getAuthIdentifierName(): string
            {
                return 'id';
            }

            public function getAuthIdentifier(): int
            {
                return 1;
            }

            public function getAuthPasswordName(): string
            {
                return 'password';
            }

            public function getAuthPassword(): string
            {
                return '';
            }

            public function getRememberToken(): ?string
            {
                return null;
            }

            public function setRememberToken($value): void {}

            public function getRememberTokenName(): ?string
            {
                return null;
            }
        };

        Gate::define('viewMailPreview', fn (Authenticatable $user): bool => $user->getAuthIdentifier() === 1);

        $this->actingAs($user);

        MailPreviewEmail::query()->create([
            'mailer' => 'array',
            'subject' => 'Allowed preview',
            'recipients' => 'reader@example.com',
            'captured_at' => now(),
        ]);

        $this->get(route('mail-preview.index'))
            ->assertOk()
            ->assertSee('Allowed preview');
    }
}
