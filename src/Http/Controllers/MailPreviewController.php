<?php

namespace LaravelSecurityAudit\MailPreview\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use LaravelSecurityAudit\MailPreview\Models\MailPreviewEmail;

class MailPreviewController
{
    public function index(): View
    {
        return view('mail-preview::index', [
            'emails' => MailPreviewEmail::query()
                ->latest('captured_at')
                ->latest('id')
                ->paginate((int) config('mail-preview.per_page', 20))
                ->withQueryString(),
        ]);
    }

    public function show(MailPreviewEmail $mailPreviewEmail): JsonResponse
    {
        return response()->json([
            'id' => $mailPreviewEmail->id,
            'mailer' => $mailPreviewEmail->mailer,
            'subject' => $mailPreviewEmail->subject,
            'sender' => $mailPreviewEmail->sender,
            'recipients' => $mailPreviewEmail->recipients,
            'cc' => $mailPreviewEmail->cc,
            'bcc' => $mailPreviewEmail->bcc,
            'reply_to' => $mailPreviewEmail->reply_to,
            'html_body' => $mailPreviewEmail->html_body,
            'text_body' => $mailPreviewEmail->text_body,
            'headers' => $mailPreviewEmail->headers,
            'attachments' => $mailPreviewEmail->attachments ?? [],
            'captured_at' => $mailPreviewEmail->captured_at?->toDayDateTimeString(),
            'delete_url' => route('mail-preview.destroy', $mailPreviewEmail),
        ]);
    }

    public function destroy(Request $request, MailPreviewEmail $mailPreviewEmail): RedirectResponse|JsonResponse
    {
        $mailPreviewEmail->delete();

        if ($request->wantsJson()) {
            return response()->json(status: 204);
        }

        return redirect()
            ->route('mail-preview.index')
            ->with('mail-preview.status', 'Email deleted.');
    }
}
