<?php

declare(strict_types=1);

use App\Actions\Logging\LogSecurity;
use App\Actions\Logging\WriteLog;

test('logs warning with security context', function (): void {
    $writeLogMock = Mockery::mock(WriteLog::class);
    $writeLogMock->shouldReceive('handle')
        ->once()
        ->with(
            'warning',
            'API',
            'Login',
            'failed login',
            Mockery::on(fn (array $ctx): bool => ($ctx['security_data'] ?? null) === ['ip' => '127.0.0.1']),
            null,
        );

    $action = new LogSecurity($writeLogMock);
    $action->handle(
        module: 'API',
        action: 'Login',
        message: 'failed login',
        data: ['ip' => '127.0.0.1'],
    );
});
