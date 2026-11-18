<?php

declare(strict_types=1);

use App\Actions\Logging\LogPerformance;
use Illuminate\Support\Facades\Log;

test('logs info with duration and metrics', function (): void {
    $log = Log::spy();

    resolve(LogPerformance::class)->handle(
        module: 'Jobs',
        action: 'Run Query',
        message: 'query completed',
        duration: 0.1505,
        metrics: ['rows' => 100],
    );

    $log->shouldHaveReceived('log')
        ->once()
        ->with('info', 'Jobs - Run Query - query completed', Mockery::on(fn (array $ctx): bool => ($ctx['duration_ms'] ?? null) === 150.5
            && ($ctx['performance_metrics'] ?? null) === ['rows' => 100]
        ));
});
