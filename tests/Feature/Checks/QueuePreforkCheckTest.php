<?php

declare(strict_types=1);

use App\Checks\QueuePreforkCheck;
use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Process;

/**
 * @param  array<string, mixed>  $overrides
 */
function preforkStatus(array $overrides = []): string
{
    return json_encode([
        'enabled'  => true,
        'source'   => 'config',
        'snapshot' => [
            'mode'   => 'prefork',
            'reason' => null,
            'units'  => [['supervisor' => 'supervisor-1', 'kind' => 'prefork']],
        ],
        'templates'    => [],
        'markers'      => [],
        'processes'    => [],
        'total_pss_mb' => 125.6,
        'degraded'     => false,
        ...$overrides,
    ], JSON_THROW_ON_ERROR);
}

it('passes and reports the total PSS while horizon workers are forked', function (): void {
    Process::fake(['*queue-prefork:status*' => Process::result(output: preforkStatus())]);

    $result = QueuePreforkCheck::new()->run();

    expect($result->status->value)->toBe('ok')
        ->and($result->getShortSummary())->toBe('Prefork')
        ->and($result->meta)->toBe(['total_pss_mb' => 125.6]);
    Process::assertRan(fn (PendingProcess $process): bool => array_slice((array) $process->command, 1) === ['artisan', 'queue-prefork:status', '--json']);
});

it('fails with the reason when horizon fell back to stock', function (): void {
    $status = preforkStatus([
        'snapshot' => ['mode' => 'stock', 'reason' => 'Horizon 5.60 changed Supervisor::monitor', 'units' => []],
        'degraded' => true,
    ]);
    Process::fake(['*' => Process::result(output: $status, exitCode: 1)]);

    $result = QueuePreforkCheck::new()->run();

    expect($result->status->value)->toBe('failed')
        ->and($result->getNotificationMessage())->toBe('Workers run stock: Horizon is in stock mode (Horizon 5.60 changed Supervisor::monitor).');
});

it('fails and names the supervisors that run stock', function (): void {
    $status = preforkStatus([
        'snapshot' => ['mode' => 'prefork', 'reason' => null, 'units' => [['supervisor' => 'supervisor-1', 'kind' => 'stock', 'reason' => 'workers kept dying']]],
        'degraded' => true,
    ]);
    Process::fake(['*' => Process::result(output: $status, exitCode: 1)]);

    $result = QueuePreforkCheck::new()->run();

    expect($result->status->value)->toBe('failed')
        ->and($result->getNotificationMessage())->toBe('Workers run stock: stock Horizon supervisors: supervisor-1 (workers kept dying).');
});

it('fails when no workers are running', function (): void {
    Process::fake(['*' => Process::result(output: preforkStatus(['snapshot' => null, 'degraded' => true]), exitCode: 1)]);

    $result = QueuePreforkCheck::new()->run();

    expect($result->status->value)->toBe('failed')
        ->and($result->getNotificationMessage())->toBe('Workers run stock: no Horizon snapshot and no queue:work template is running.');
});

it('warns while prefork is switched off', function (): void {
    Process::fake(['*' => Process::result(output: preforkStatus(['enabled' => false, 'snapshot' => ['mode' => 'stock', 'reason' => 'disabled', 'units' => []], 'degraded' => true]), exitCode: 1)]);

    $result = QueuePreforkCheck::new()->run();

    expect($result->status->value)->toBe('warning')
        ->and($result->getShortSummary())->toBe('Disabled');
});

it('fails when the status command does not print json', function (): void {
    Process::fake(['*' => Process::result(errorOutput: 'Command "queue-prefork:status" is not defined.', exitCode: 1)]);

    $result = QueuePreforkCheck::new()->run();

    expect($result->status->value)->toBe('failed')
        ->and($result->getNotificationMessage())->toBe('Could not read the queue-prefork status: Command "queue-prefork:status" is not defined.');
});
