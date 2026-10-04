<?php

declare(strict_types=1);

use App\Jobs\LogActivity;
use App\Jobs\NotifyFailedJob;
use App\Mail\FailedJobNotification;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Queue\Events\JobFailed;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Queue;

beforeEach(function (): void {
    Config::set('queue.failed.notification_emails', ['dev@example.com']);
});

test('does not dispatch NotifyFailedJob when NotifyFailedJob itself fails', function (): void {
    Queue::fake();

    $job = createMockJob(
        payload: [
            'uuid'        => 'test-uuid-456',
            'displayName' => NotifyFailedJob::class,
            'data'        => ['commandName' => NotifyFailedJob::class],
        ],
        queue: 'emails',
        attempts: 1,
    );

    Event::dispatch(new JobFailed('redis', $job, new Exception('Mail failed')));

    Queue::assertNotPushed(NotifyFailedJob::class);
});

test('does not dispatch NotifyFailedJob when notification emails are empty', function (): void {
    Config::set('queue.failed.notification_emails', []);
    Queue::fake();

    $job = createMockJob(
        payload: [
            'uuid'        => 'test-uuid-789',
            'displayName' => LogActivity::class,
            'data'        => ['commandName' => LogActivity::class],
        ],
        queue: 'default',
        attempts: 1,
    );

    Event::dispatch(new JobFailed('redis', $job, new Exception('Job failed')));

    Queue::assertNotPushed(NotifyFailedJob::class);
});

test('NotifyFailedJob is queued on emails queue', function (): void {
    Queue::fake();

    $job = createMockJob(
        payload: [
            'uuid'        => 'test-uuid-queue',
            'displayName' => LogActivity::class,
            'data'        => ['commandName' => LogActivity::class],
        ],
        queue: 'default',
        attempts: 1,
    );

    Event::dispatch(new JobFailed('redis', $job, new Exception('Job failed')));

    $queueName = Config::string('queue.queues.emails');
    Queue::assertPushedOn($queueName, NotifyFailedJob::class);
});

test('handle sends FailedJobNotification to each configured email', function (): void {
    Mail::fake();

    $job = new NotifyFailedJob(
        connectionName: 'redis',
        failedQueue: 'default',
        uuid: 'test-uuid-123',
        jobName: 'App\Jobs\ProcessOrder',
        exceptionClass: 'RuntimeException',
        exceptionMessage: 'Something went wrong',
        stackTrace: "line 1\nline 2",
        failedAt: now()->toIso8601String(),
        attempts: 3,
    );

    Config::set('queue.failed.notification_emails', ['dev@example.com', 'ops@example.com']);

    $job->handle();

    Mail::assertSent(FailedJobNotification::class, 2);
    Mail::assertSent(FailedJobNotification::class, fn ($mailable) => $mailable->hasTo('dev@example.com'));
    Mail::assertSent(FailedJobNotification::class, fn ($mailable) => $mailable->hasTo('ops@example.com'));
});

test('handle truncates stack trace when over 50 lines', function (): void {
    $lines      = array_fill(0, 60, 'trace line');
    $stackTrace = implode("\n", $lines);

    $job = new NotifyFailedJob(
        connectionName: 'redis',
        failedQueue: 'default',
        uuid: 'test-uuid',
        jobName: 'App\Jobs\ProcessOrder',
        exceptionClass: 'RuntimeException',
        exceptionMessage: 'Error',
        stackTrace: $stackTrace,
        failedAt: now()->toIso8601String(),
        attempts: 1,
    );

    Mail::fake();

    $job->handle();

    Mail::assertSent(FailedJobNotification::class, fn (FailedJobNotification $mailable): bool => str_contains($mailable->stackTrace, '... (truncated, 10 more lines)'));
});

test('handle does nothing when notification emails is empty', function (): void {
    Mail::fake();

    Config::set('queue.failed.notification_emails', []);

    $job = new NotifyFailedJob(
        connectionName: 'redis',
        failedQueue: 'default',
        uuid: 'test-uuid',
        jobName: 'App\Jobs\ProcessOrder',
        exceptionClass: 'RuntimeException',
        exceptionMessage: 'Error',
        stackTrace: 'trace',
        failedAt: now()->toIso8601String(),
        attempts: 1,
    );

    $job->handle();

    Mail::assertNothingSent();
});

test('job is queued on emails queue when dispatched', function (): void {
    Queue::fake();

    dispatch(new NotifyFailedJob(
        connectionName: 'redis',
        failedQueue: 'default',
        uuid: 'test-uuid',
        jobName: 'App\Jobs\ProcessOrder',
        exceptionClass: 'RuntimeException',
        exceptionMessage: 'Error',
        stackTrace: 'trace',
        failedAt: now()->toIso8601String(),
        attempts: 1,
    ));

    $queueName = Config::string('queue.queues.emails');
    Queue::assertPushedOn($queueName, NotifyFailedJob::class);
});

test('job uses default queue when emails config is null', function (): void {
    $originalConfig = Config::string('queue.queues.emails');

    config(['queue.queues.emails' => null]);

    Queue::fake();

    dispatch(new NotifyFailedJob(
        connectionName: 'redis',
        failedQueue: 'default',
        uuid: 'test-uuid',
        jobName: 'App\Jobs\ProcessOrder',
        exceptionClass: 'RuntimeException',
        exceptionMessage: 'Error',
        stackTrace: 'trace',
        failedAt: now()->toIso8601String(),
        attempts: 1,
    ));

    Queue::assertPushedOn('default', NotifyFailedJob::class);

    config(['queue.queues.emails' => $originalConfig]);
});

test('job has one try', function (): void {
    $job = new NotifyFailedJob(
        connectionName: 'redis',
        failedQueue: 'default',
        uuid: 'test-uuid',
        jobName: 'App\Jobs\ProcessOrder',
        exceptionClass: 'RuntimeException',
        exceptionMessage: 'Error',
        stackTrace: 'trace',
        failedAt: now()->toIso8601String(),
        attempts: 1,
    );

    expect($job->tries)->toBe(1);
});

/**
 * @param  array<string, mixed>  $payload
 */
function createMockJob(array $payload, string $queue, int $attempts): Job
{
    $job = Mockery::mock(Job::class);
    $job->allows('payload')->andReturn($payload);
    $job->allows('getQueue')->andReturn($queue);
    $job->allows('attempts')->andReturn($attempts);
    $job->allows('getJobId')->andReturn('test-job-id');

    return $job;
}
