<?php

declare(strict_types=1);

use App\Actions\Logging\LogAudit;
use App\Actions\Logging\WriteLog;

test('logs info with audit context', function (): void {
    $writeLogMock = Mockery::mock(WriteLog::class);
    $writeLogMock->shouldReceive('handle')
        ->once()
        ->with(
            'info',
            'CMS',
            'Login',
            'user logged in',
            Mockery::on(fn (array $ctx): bool => ($ctx['audit_data'] ?? null) === ['user_id' => 1]),
            null,
        );

    $action = new LogAudit($writeLogMock);
    $action->handle(
        module: 'CMS',
        action: 'Login',
        message: 'user logged in',
        data: ['user_id' => 1],
    );
});
