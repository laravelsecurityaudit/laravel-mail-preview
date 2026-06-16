<?php

use Illuminate\Support\Facades\Route;
use LaravelSecurityAudit\MailPreview\Http\Controllers\MailPreviewController;
use LaravelSecurityAudit\MailPreview\Http\Middleware\AuthorizeMailPreview;

$middleware = (array) config('mail-preview.middleware', ['web']);
$middleware[] = AuthorizeMailPreview::class;

Route::middleware($middleware)
    ->prefix(trim((string) config('mail-preview.path', '_testing/emails'), '/'))
    ->name('mail-preview.')
    ->group(function (): void {
        Route::get('/', [MailPreviewController::class, 'index'])->name('index');
        Route::get('/{mailPreviewEmail}', [MailPreviewController::class, 'show'])->name('show');
        Route::delete('/{mailPreviewEmail}', [MailPreviewController::class, 'destroy'])->name('destroy');
    });
