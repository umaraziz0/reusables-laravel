<?php

declare(strict_types=1);

use App\Actions\Logging\LogAudit;
use Illuminate\Support\Facades\Log;

test('logs info with audit context', function (): void {
    $log = Log::spy();

    resolve(LogAudit::class)->handle(
        module: 'CMS',
        action: 'Login',
        message: 'user logged in',
        data: ['user_id' => 1],
    );

    $log->shouldHaveReceived('log')
        ->once()
        ->with('info', 'CMS - Login - user logged in', Mockery::on(fn (array $ctx): bool => ($ctx['audit_data'] ?? null) === ['user_id' => 1]));
});
