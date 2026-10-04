<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\FailedJobNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class NotifyFailedJob implements ShouldQueue
{
    use Queueable;

    /**
     * Notification delivery is not idempotent, so retries may send duplicate emails.
     */
    public int $tries = 1;

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
    ) {
        $queueName = Config::get('queue.queues.emails');

        $this->onQueue(is_string($queueName) ? $queueName : 'default');
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $emails = Config::array('queue.failed.notification_emails', []);

        $maxLines  = 50;
        $lines     = explode("\n", $this->stackTrace);
        $truncated = implode("\n", array_slice($lines, 0, $maxLines));

        if (count($lines) > $maxLines) {
            $truncated .= "\n... (truncated, " . (count($lines) - $maxLines) . ' more lines)';
        }

        foreach ($emails as $email) {
            Mail::to($email)->send(new FailedJobNotification(
                connectionName: $this->connectionName,
                failedQueue: $this->failedQueue,
                uuid: $this->uuid,
                jobName: $this->jobName,
                exceptionClass: $this->exceptionClass,
                exceptionMessage: $this->exceptionMessage,
                stackTrace: $truncated,
                failedAt: $this->failedAt,
                attempts: $this->attempts,
            ));
        }
    }
}
