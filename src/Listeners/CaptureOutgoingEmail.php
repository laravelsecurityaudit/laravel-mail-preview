<?php

namespace LaravelSecurityAudit\MailPreview\Listeners;

use Illuminate\Mail\Events\MessageSending;
use LaravelSecurityAudit\MailPreview\Models\MailPreviewEmail;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;
use Throwable;

class CaptureOutgoingEmail
{
    public function handle(MessageSending $event): void
    {
        if (! config('mail-preview.enabled')) {
            return;
        }

        try {
            $message = $event->message;

            MailPreviewEmail::create([
                'mailer' => $event->data['mailer'] ?? config('mail.default'),
                'subject' => $message->getSubject(),
                'sender' => $this->formatAddresses($message->getFrom()),
                'recipients' => $this->formatAddresses($message->getTo()),
                'cc' => $this->formatAddresses($message->getCc()),
                'bcc' => $this->formatAddresses($message->getBcc()),
                'reply_to' => $this->formatAddresses($message->getReplyTo()),
                'from_addresses' => $this->normalizeAddresses($message->getFrom()),
                'to_addresses' => $this->normalizeAddresses($message->getTo()),
                'cc_addresses' => $this->normalizeAddresses($message->getCc()),
                'bcc_addresses' => $this->normalizeAddresses($message->getBcc()),
                'reply_to_addresses' => $this->normalizeAddresses($message->getReplyTo()),
                'html_body' => $message->getHtmlBody(),
                'text_body' => $message->getTextBody(),
                'headers' => $message->getHeaders()->toString(),
                'attachments' => $this->normalizeAttachments($message),
                'data_keys' => array_keys($event->data),
                'captured_at' => now(),
            ]);
        } catch (Throwable $exception) {
            report($exception);
        }
    }

    /**
     * @param  array<int, Address>  $addresses
     */
    private function formatAddresses(array $addresses): ?string
    {
        if ($addresses === []) {
            return null;
        }

        return collect($addresses)
            ->map(function (Address $address): string {
                if ($address->getName() === '') {
                    return $address->getAddress();
                }

                return $address->getName().' <'.$address->getAddress().'>';
            })
            ->implode(', ');
    }

    /**
     * @param  array<int, Address>  $addresses
     * @return array<int, array{address: string, name: string|null}>
     */
    private function normalizeAddresses(array $addresses): array
    {
        return collect($addresses)
            ->map(fn (Address $address): array => [
                'address' => $address->getAddress(),
                'name' => $address->getName() !== '' ? $address->getName() : null,
            ])
            ->values()
            ->all();
    }

    /**
     * @return array<int, array{filename: string|null, content_type: string|null, disposition: string|null, content_id: string|null}>
     */
    private function normalizeAttachments(Email $message): array
    {
        return collect($message->getAttachments())
            ->map(fn (DataPart $attachment): array => [
                'filename' => $attachment->getFilename(),
                'content_type' => $attachment->getContentType(),
                'disposition' => $attachment->getDisposition(),
                'content_id' => $attachment->hasContentId() ? $attachment->getContentId() : null,
            ])
            ->values()
            ->all();
    }
}
