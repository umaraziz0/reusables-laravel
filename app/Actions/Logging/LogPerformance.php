<?php

declare(strict_types=1);

namespace App\Actions\Logging;

use Illuminate\Http\Request;

class LogPerformance
{
    public function __construct(
        private readonly WriteLog $writeLog,
    ) {}

    /**
     * @param  array<string, mixed>  $metrics
     */
    public function handle(
        string $module,
        string $action,
        string $message,
        float $duration,
        array $metrics = [],
        ?Request $request = null,
    ): void {
        $this->writeLog->handle(
            level: 'info',
            module: $module,
            action: $action,
            message: $message,
            context: [
                'duration_ms'         => round($duration * 1000, 2),
                'performance_metrics' => $metrics,
            ],
            request: $request,
        );
    }
}
