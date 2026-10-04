<?php

declare(strict_types=1);

namespace App\Actions\Logging;

use Illuminate\Http\Request;

class LogSecurity
{
    public function __construct(
        private readonly WriteLog $writeLog,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(
        string $module,
        string $action,
        string $message,
        array $data = [],
        ?Request $request = null,
    ): void {
        $this->writeLog->handle(
            level: 'warning',
            module: $module,
            action: $action,
            message: $message,
            context: ['security_data' => $data],
            request: $request,
        );
    }
}
