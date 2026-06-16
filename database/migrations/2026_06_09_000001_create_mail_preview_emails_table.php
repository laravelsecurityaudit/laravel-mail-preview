<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $tableName = (string) config('mail-preview.table', 'mail_preview_emails');

        if (Schema::hasTable($tableName)) {
            return;
        }

        Schema::create($tableName, function (Blueprint $table): void {
            $table->id();
            $table->string('mailer')->nullable()->index();
            $table->string('subject')->nullable()->index();
            $table->text('sender')->nullable();
            $table->text('recipients')->nullable();
            $table->text('cc')->nullable();
            $table->text('bcc')->nullable();
            $table->text('reply_to')->nullable();
            $table->json('from_addresses')->nullable();
            $table->json('to_addresses')->nullable();
            $table->json('cc_addresses')->nullable();
            $table->json('bcc_addresses')->nullable();
            $table->json('reply_to_addresses')->nullable();
            $table->longText('html_body')->nullable();
            $table->longText('text_body')->nullable();
            $table->longText('headers')->nullable();
            $table->json('attachments')->nullable();
            $table->json('data_keys')->nullable();
            $table->timestamp('captured_at')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if ((string) config('mail-preview.table', 'mail_preview_emails') !== 'mail_preview_emails') {
            return;
        }

        Schema::dropIfExists('mail_preview_emails');
    }
};
