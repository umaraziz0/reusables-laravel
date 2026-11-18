<?php

declare(strict_types=1);

use App\Actions\Logging\LogException;
use Illuminate\Support\Facades\Log;

test('logs error with exception context', function (): void {
    $log = Log::spy();

    resolve(LogException::class)->handle(
        module: 'API',
        action: 'Create User',
        message: 'failed to create user',
        exception: new RuntimeException('Test error'),
        additionalContext: ['extra' => 'data'],
    );

    $log->shouldHaveReceived('log')
        ->once()
        ->with('error', 'API - Create User - failed to create user', Mockery::on(fn (array $ctx): bool => ($ctx['exception_class'] ?? null) === RuntimeException::class
            && isset($ctx['exception_message'])
            && isset($ctx['exception_trace'])
            && ($ctx['context'] ?? null) === ['extra' => 'data']
        ));
});
