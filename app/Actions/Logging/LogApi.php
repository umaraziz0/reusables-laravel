<?php

declare(strict_types=1);

namespace App\Actions\Logging;

use Illuminate\Http\Request;

class LogApi
{
    public function __construct(
        private readonly WriteLog $writeLog,
    ) {}

    /**
     * @param  array<string, mixed>  $requestData
     * @param  array<string, mixed>  $responseData
     */
    public function handle(
        string $module,
        string $action,
        string $message,
        array $requestData = [],
        array $responseData = [],
        ?int $statusCode = null,
        ?Request $request = null,
    ): void {
        $this->writeLog->handle(
            level: 'info',
            module: $module,
            action: $action,
            message: $message,
            context: [
                'request_data'  => $requestData,
                'response_data' => $responseData,
                'status_code'   => $statusCode,
            ],
            request: $request,
        );
    }
}
