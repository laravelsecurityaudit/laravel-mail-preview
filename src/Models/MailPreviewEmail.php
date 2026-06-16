<?php

namespace LaravelSecurityAudit\MailPreview\Models;

use Illuminate\Database\Eloquent\Model;

class MailPreviewEmail extends Model
{
    protected $fillable = [
        'mailer',
        'subject',
        'sender',
        'recipients',
        'cc',
        'bcc',
        'reply_to',
        'from_addresses',
        'to_addresses',
        'cc_addresses',
        'bcc_addresses',
        'reply_to_addresses',
        'html_body',
        'text_body',
        'headers',
        'attachments',
        'data_keys',
        'captured_at',
    ];

    public function getTable(): string
    {
        return (string) config('mail-preview.table', 'mail_preview_emails');
    }

    protected function casts(): array
    {
        return [
            'from_addresses' => 'array',
            'to_addresses' => 'array',
            'cc_addresses' => 'array',
            'bcc_addresses' => 'array',
            'reply_to_addresses' => 'array',
            'attachments' => 'array',
            'data_keys' => 'array',
            'captured_at' => 'datetime',
        ];
    }
}
