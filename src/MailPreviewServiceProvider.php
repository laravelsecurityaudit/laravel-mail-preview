<?php

namespace LaravelSecurityAudit\MailPreview;

use Illuminate\Mail\Events\MessageSending;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use LaravelSecurityAudit\MailPreview\Listeners\CaptureOutgoingEmail;

class MailPreviewServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/mail-preview.php', 'mail-preview');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'mail-preview');
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/mail-preview.php' => config_path('mail-preview.php'),
            ], 'mail-preview-config');

            $this->publishes([
                __DIR__.'/../resources/views' => resource_path('views/vendor/mail-preview'),
            ], 'mail-preview-views');

            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'mail-preview-migrations');
        }

        if (! config('mail-preview.enabled')) {
            return;
        }

        Event::listen(MessageSending::class, CaptureOutgoingEmail::class);

        $this->loadRoutesFrom(__DIR__.'/../routes/web.php');
    }
}
