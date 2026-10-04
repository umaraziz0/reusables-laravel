<?php

declare(strict_types=1);

namespace App\Actions\Logging;

use Illuminate\Http\Request;
use Throwable;

class LogException
{
    public function __construct(
        private readonly WriteLog $writeLog,
    ) {}

    /**
     * @param  array<string, mixed>  $additionalContext
     */
    public function handle(
        string $module,
        string $action,
        string $message,
        Throwable $exception,
        array $additionalContext = [],
        ?Request $request = null,
    ): void {
        $this->writeLog->handle(
            level: 'error',
            module: $module,
            action: $action,
            message: $message,
            context: [
                'context'           => $additionalContext,
                'exception_class'   => $exception::class,
                'exception_message' => $exception->getMessage(),
                'exception_code'    => $exception->getCode(),
                'exception_file'    => $exception->getFile(),
                'exception_line'    => $exception->getLine(),
                'exception_trace'   => $exception->getTraceAsString(),
            ],
            request: $request,
        );
    }
}
