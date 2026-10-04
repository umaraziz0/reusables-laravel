<?php

declare(strict_types=1);

use App\Actions\Logging\WriteLog;
use App\Models\User;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;
use Illuminate\Session\ArraySessionHandler;
use Illuminate\Session\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

test('joins module action and message into the log line', function (): void {
    Log::shouldReceive('log')
        ->once()
        ->with(
            'info',
            'API - Create User - user created',
            Mockery::on(fn (array $context): bool => isset($context['timestamp'])
                && ($context['custom'] ?? null) === 'value'),
        );

    $action = new WriteLog();
    $action->handle(
        level: 'info',
        module: 'API',
        action: 'Create User',
        message: 'user created',
        context: ['custom' => 'value'],
    );
});

test('includes auth user uuid when authenticated', function (): void {
    $user       = new User();
    $user->uuid = 'test-user-uuid';

    Auth::setUser($user);

    Log::shouldReceive('log')
        ->once()
        ->with(
            'info',
            'API - Create User - user created',
            Mockery::on(fn (array $context): bool => ($context['auth_user_uuid'] ?? null) === $user->uuid),
        );

    $action = new WriteLog();
    $action->handle(
        level: 'info',
        module: 'API',
        action: 'Create User',
        message: 'user created',
    );
});

test('uses provided request for context', function (): void {
    $request = Request::create('/test', 'GET');

    Log::shouldReceive('log')
        ->once()
        ->with(
            'info',
            'API - Create User - user created',
            Mockery::on(fn (array $context): bool => ($context['url'] ?? null) === $request->fullUrl()
                && ($context['method'] ?? null) === 'GET'),
        );

    $action = new WriteLog();
    $action->handle(
        level: 'info',
        module: 'API',
        action: 'Create User',
        message: 'user created',
        request: $request,
    );
});

test('includes the attached session id in log context', function (): void {
    $request   = Request::create('/test', 'GET');
    $session   = new Store('test', new ArraySessionHandler(120));
    $sessionId = str_repeat('a', 40);
    $session->setId($sessionId);
    $request->setLaravelSession($session);

    Log::shouldReceive('log')
        ->once()
        ->with(
            'info',
            'API - Create User - user created',
            Mockery::on(fn (array $context): bool => ($context['session_id'] ?? null) === $sessionId),
        );

    new WriteLog()->handle('info', 'API', 'Create User', 'user created', request: $request);
});

test('logs a request without a session using a null session id', function (): void {
    $request = Request::create('/test', 'GET');

    Log::shouldReceive('log')
        ->once()
        ->with(
            'info',
            'API - Create User - user created',
            Mockery::on(fn (array $context): bool => array_key_exists('session_id', $context)
                && $context['session_id'] === null),
        );

    new WriteLog()->handle('info', 'API', 'Create User', 'user created', request: $request);
});

test('does not hide an unexpected session id failure', function (): void {
    $request = Request::create('/test', 'GET');
    $session = Mockery::mock(Session::class);
    $session->shouldReceive('getId')->once()->andThrow(new RuntimeException('Session ID unavailable'));
    $request->setLaravelSession($session);

    Log::shouldReceive('log')->never();

    expect(fn () => new WriteLog()->handle('info', 'API', 'Create User', 'user created', request: $request))
        ->toThrow(RuntimeException::class, 'Session ID unavailable');
});
