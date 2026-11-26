<?php

declare(strict_types=1);

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Process;

use function Illuminate\Support\php_binary;

/**
 * Evaluate config/horizon.php (and through it config/queue.php) in a fresh PHP process with the given
 * environment variables, as a worker host would.
 *
 * @param  array<string, string>  $environment
 * @return array<string, mixed>
 */
function horizonConfigWith(array $environment): array
{
    $script = 'require "vendor/autoload.php"; echo json_encode(require "config/horizon.php", JSON_THROW_ON_ERROR);';

    $process = Process::path(base_path())->env($environment)->run([php_binary(), '-r', $script])->throw();

    $config = json_decode($process->output(), true, flags: JSON_THROW_ON_ERROR);

    return is_array($config) ? $config : [];
}

/**
 * @param  array<array-key, mixed>  $horizon
 * @return list<string>
 */
function workedQueues(array $horizon): array
{
    $queues = [];

    foreach (Arr::wrap($horizon['defaults'] ?? []) as $supervisor) {
        foreach (Arr::wrap(is_array($supervisor) ? ($supervisor['queue'] ?? []) : []) as $queue) {
            if (is_string($queue)) {
                $queues[] = $queue;
            }
        }
    }

    return $queues;
}

test('every enabled queue, including the connection default, is worked by exactly one supervisor', function (): void {
    $queues   = Config::array('queue.queues');
    $disabled = Config::array('queue.disabled');

    $expected = array_values(array_diff_key($queues, array_flip($disabled)));

    expect(workedQueues(Config::array('horizon')))->toEqualCanonicalizing($expected)
        ->toContain(Config::string('queue.connections.redis.queue'));
});

test('every supervisor times jobs out before the redis connection retries them', function (): void {
    $retryAfter = Config::integer('queue.connections.redis.retry_after');

    foreach (Config::array('horizon.defaults') as $supervisor) {
        expect($supervisor['timeout'])->toBeLessThan($retryAfter);
    }
});

test('exports run on their own supervisor', function (): void {
    expect(Config::array('horizon.defaults.supervisor-exports.queue'))->toBe([Config::string('queue.queues.exports')])
        ->and(Config::array('horizon.defaults.supervisor-default.queue'))->not->toContain(Config::string('queue.queues.exports'));
});

test('the telescope queue is worked only locally', function (): void {
    $local      = horizonConfigWith(['APP_NAME' => 'Acme', 'APP_ENV' => 'local', 'QUEUE_DISABLED' => '']);
    $production = horizonConfigWith(['APP_NAME' => 'Acme', 'APP_ENV' => 'production', 'QUEUE_DISABLED' => '']);

    expect(workedQueues($local))->toContain('acme-local-queue-telescope')
        ->and(workedQueues($production))->toContain('acme-production-queue-emails')
        ->not->toContain('acme-production-queue-telescope');
});

test('a queue listed in QUEUE_DISABLED gets no worker and no long-wait threshold', function (): void {
    $horizon = horizonConfigWith(['APP_NAME' => 'Acme', 'APP_ENV' => 'production', 'QUEUE_DISABLED' => 'storage, logs_message']);

    expect(workedQueues($horizon))->toContain('acme-production-queue-emails')
        ->not->toContain('acme-production-queue-storage')
        ->not->toContain('acme-production-queue-logs-message')
        ->and($horizon['waits'])->toHaveKey('redis:acme-production-queue-emails')
        ->not->toHaveKey('redis:acme-production-queue-storage');
});

test('disabling exports removes its supervisor from every environment', function (): void {
    $horizon = horizonConfigWith(['APP_NAME' => 'Acme', 'APP_ENV' => 'production', 'QUEUE_DISABLED' => 'exports']);

    expect($horizon['defaults'])->toHaveKey('supervisor-default')
        ->not->toHaveKey('supervisor-exports')
        ->and($horizon['environments']['production'])->not->toHaveKey('supervisor-exports')
        ->and($horizon['environments']['*'])->not->toHaveKey('supervisor-exports');
});
