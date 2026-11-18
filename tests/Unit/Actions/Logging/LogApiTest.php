<?php

declare(strict_types=1);

use App\Actions\Logging\LogApi;
use Illuminate\Support\Facades\Log;

test('logs info with endpoint and request response data', function (): void {
    $log = Log::spy();

    resolve(LogApi::class)->handle(
        module: 'API',
        action: 'List Users',
        message: 'users listed',
        requestData: ['page' => 1],
        responseData: ['total' => 10],
        statusCode: 200,
    );

    $log->shouldHaveReceived('log')
        ->once()
        ->with('info', 'API - List Users - users listed', Mockery::on(fn (array $ctx): bool => ($ctx['request_data'] ?? null) === ['page' => 1]
            && ($ctx['response_data'] ?? null) === ['total' => 10]
            && ($ctx['status_code'] ?? null) === 200
        ));
});
