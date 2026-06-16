# Laravel Mail Preview

[![Latest Version on Packagist](https://img.shields.io/packagist/v/laravelsecurityaudit/laravel-mail-preview.svg?style=flat-square)](https://packagist.org/packages/laravelsecurityaudit/laravel-mail-preview)
[![Total Downloads](https://img.shields.io/packagist/dt/laravelsecurityaudit/laravel-mail-preview.svg?style=flat-square)](https://packagist.org/packages/laravelsecurityaudit/laravel-mail-preview)
[![Tests](https://github.com/workaandrey/laravel-mail-preview/actions/workflows/tests.yml/badge.svg)](https://github.com/workaandrey/laravel-mail-preview/actions/workflows/tests.yml)
[![License](https://img.shields.io/packagist/l/laravelsecurityaudit/laravel-mail-preview.svg?style=flat-square)](LICENSE)

Database-backed mail preview inbox for Laravel applications.

Capture every outgoing email your app sends, then browse them in a built-in web UI — no Mailtrap account, no extra SMTP service, and no mail driver swap required.

## Why this package

- **Works with your existing mail stack** — listens to Laravel's `MessageSending` event instead of replacing mail drivers.
- **Uses your app's database** — captured messages are stored in a normal Eloquent table you can query or prune.
- **Self-contained UI** — dark-themed inbox with HTML, plain text, headers, and attachment metadata views.
- **Safe by default** — disabled in production unless you explicitly enable it.
- **Laravel 11, 12, and 13** — tested with Orchestra Testbench.

## Requirements

- PHP 8.2+
- Laravel 11, 12, or 13

## Installation

```bash
composer require laravelsecurityaudit/laravel-mail-preview:^1.1
php artisan migrate
```

The package auto-registers its service provider through Laravel package discovery.

## Quick start

1. Install the package and run migrations.
2. In local or staging, open `/_testing/emails` (default path).
3. Trigger any mail send (`Mail::send`, notifications, etc.).
4. Refresh the inbox to inspect captured messages.

The inbox is **disabled in production** unless you set `MAIL_PREVIEW_ENABLED=true`.

## Configuration

Publish the config when you need to customize defaults:

```bash
php artisan vendor:publish --tag=mail-preview-config
```

Environment variables:

```dotenv
MAIL_PREVIEW_ENABLED=true
MAIL_PREVIEW_PATH=_testing/emails
MAIL_PREVIEW_TABLE=mail_preview_emails
MAIL_PREVIEW_PER_PAGE=20
MAIL_PREVIEW_GATE=viewMailPreview
```

`DEBUG_MAIL_INBOX_ENABLED` is also supported as a backward-compatible enablement flag.

### Protecting the inbox

By default, the route uses only the `web` middleware group. **Always protect staging and shared environments.**

Publish the config and restrict access:

```php
'middleware' => ['web', 'auth'],
'gate' => 'viewMailPreview',
```

Register the gate in `AppServiceProvider`:

```php
use Illuminate\Support\Facades\Gate;

Gate::define('viewMailPreview', fn ($user) => $user->isAdmin());
```

Captured emails may contain password reset links, receipts, and other sensitive data. Treat the inbox like application logs.

## Publishing assets

```bash
php artisan vendor:publish --tag=mail-preview-config
php artisan vendor:publish --tag=mail-preview-views
php artisan vendor:publish --tag=mail-preview-migrations
```

## What gets captured

Each message stores:

- mailer name, subject, from/to/cc/bcc/reply-to
- rendered HTML and plain-text bodies
- raw headers
- attachment metadata (filename, content type, disposition — not file contents)
- Laravel mail event data keys

## Testing

```bash
composer test
```

## Changelog

See [CHANGELOG.md](CHANGELOG.md).

## License

The MIT License (MIT). Please see [LICENSE](LICENSE) for more information.
