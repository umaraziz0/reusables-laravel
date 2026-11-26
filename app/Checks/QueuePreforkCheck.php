<?php

declare(strict_types=1);

namespace App\Checks;

use Illuminate\Support\Facades\Process;
use JsonException;
use Spatie\Health\Checks\Check;
use Spatie\Health\Checks\Result;

use function Illuminate\Support\php_binary;

/**
 * Fails when queue-prefork has fallen back to stock workers. A Horizon release that changes the
 * internals the package extends makes it run stock without an error, so this is what notices.
 */
class QueuePreforkCheck extends Check
{
    public function run(): Result
    {
        $process = Process::path(base_path())
            ->timeout(30)
            ->run([php_binary(), 'artisan', 'queue-prefork:status', '--json']);

        try {
            $status = json_decode($process->output(), true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return Result::make()
                ->failed('Could not read the queue-prefork status: ' . mb_trim($process->errorOutput() ?: $process->output()))
                ->shortSummary('Unknown');
        }

        if (! is_array($status)) {
            return Result::make()->failed('The queue-prefork status is not a JSON object.')->shortSummary('Unknown');
        }

        if (($status['enabled'] ?? false) !== true) {
            return Result::make()
                ->warning('queue-prefork is disabled, so every worker runs stock.')
                ->shortSummary('Disabled');
        }

        if (($status['degraded'] ?? true) !== false) {
            return Result::make()
                ->failed('Workers run stock: ' . $this->degradedReason($status) . '.')
                ->shortSummary('Stock')
                ->meta(['snapshot' => $status['snapshot'] ?? null, 'markers' => $status['markers'] ?? []]);
        }

        return Result::make()
            ->ok()
            ->shortSummary('Prefork')
            ->meta(['total_pss_mb' => $status['total_pss_mb'] ?? null]);
    }

    /**
     * @param  array<array-key, mixed>  $status
     */
    private function degradedReason(array $status): string
    {
        $snapshot = is_array($status['snapshot'] ?? null) ? $status['snapshot'] : null;
        $mode     = is_string($snapshot['mode'] ?? null) ? $snapshot['mode'] : 'unknown';

        if ($snapshot !== null && $mode !== 'prefork') {
            $reason = is_string($snapshot['reason'] ?? null) ? $snapshot['reason'] : 'no reason given';

            return "Horizon is in {$mode} mode ({$reason})";
        }

        $stockSupervisors = collect(is_array($snapshot['units'] ?? null) ? $snapshot['units'] : [])
            ->filter(fn (mixed $unit): bool => is_array($unit) && ($unit['kind'] ?? null) === 'stock')
            ->map(fn (array $unit): string => sprintf('%s (%s)', $unit['supervisor'] ?? 'unknown', $unit['reason'] ?? 'no reason given'));

        if ($stockSupervisors->isNotEmpty()) {
            return 'stock Horizon supervisors: ' . $stockSupervisors->implode(', ');
        }

        if (($status['markers'] ?? []) !== []) {
            return 'a queue:work template is held stock';
        }

        return 'no Horizon snapshot and no queue:work template is running';
    }
}
