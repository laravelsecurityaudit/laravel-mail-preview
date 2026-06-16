<?php

namespace LaravelSecurityAudit\MailPreview\Tests;

use LaravelSecurityAudit\MailPreview\MailPreviewServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            MailPreviewServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('database.default', 'testing');
        $app['config']->set('database.connections.testing', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $app['config']->set('mail.default', 'array');
        $app['config']->set('mail.from.address', 'sender@example.com');
        $app['config']->set('mail.from.name', 'Mail Preview Sender');
        $app['config']->set('mail-preview.enabled', true);
        $app['config']->set('mail-preview.path', '_testing/emails');
        $app['config']->set('mail-preview.middleware', ['web']);
        $app['config']->set('mail-preview.gate', null);
        $app['config']->set('mail-preview.table', 'mail_preview_emails');
        $app['config']->set('mail-preview.per_page', 20);
    }

    protected function runPackageMigrations(): void
    {
        $this->artisan('migrate', ['--database' => 'testing'])->run();
    }
}
