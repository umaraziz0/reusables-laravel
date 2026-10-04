<?php

declare(strict_types=1);

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Config;

class FailedJobNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $connectionName,
        public string $failedQueue,
        public ?string $uuid,
        public string $jobName,
        public string $exceptionClass,
        public string $exceptionMessage,
        public string $stackTrace,
        public string $failedAt,
        public ?int $attempts = null,
    ) {}

    public function envelope(): Envelope
    {
        $subject = '[' . Config::string('app.name') . '] Queue Job Failed: ' . $this->jobName;

        return new Envelope(
            subject: $subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.failed-job-notification',
        );
    }

    /**
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
