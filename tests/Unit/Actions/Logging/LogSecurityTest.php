<?php

declare(strict_types=1);

use App\Actions\Logging\LogSecurity;
use Illuminate\Support\Facades\Log;

test('logs warning with security context', function (): void {
    $log = Log::spy();

    resolve(LogSecurity::class)->handle(
        module: 'API',
        action: 'Login',
        message: 'failed login',
        data: ['ip' => '127.0.0.1'],
    );

    $log->shouldHaveReceived('log')
        ->once()
        ->with('warning', 'API - Login - failed login', Mockery::on(fn (array $ctx): bool => ($ctx['security_data'] ?? null) === ['ip' => '127.0.0.1']));
});
