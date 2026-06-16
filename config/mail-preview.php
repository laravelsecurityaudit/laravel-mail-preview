<?php

return [
    'enabled' => env('MAIL_PREVIEW_ENABLED', env('DEBUG_MAIL_INBOX_ENABLED', ! app()->isProduction())),

    'path' => trim((string) env('MAIL_PREVIEW_PATH', '_testing/emails'), '/'),

    'middleware' => ['web'],

    'gate' => env('MAIL_PREVIEW_GATE'),

    'table' => env('MAIL_PREVIEW_TABLE', 'mail_preview_emails'),

    'per_page' => (int) env('MAIL_PREVIEW_PER_PAGE', 20),
];
