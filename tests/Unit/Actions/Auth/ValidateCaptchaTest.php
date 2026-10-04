<?php

declare(strict_types=1);

use App\Actions\Auth\ValidateCaptcha;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

beforeEach(function (): void {
    Config::set('services.turnstile.secret_key', 'test-secret-key');
});

test('returns true when Cloudflare Turnstile returns success', function (): void {
    Log::shouldReceive('log')->never();

    Http::fake([
        'challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true], 200),
    ]);

    $result = resolve(ValidateCaptcha::class)->handle('valid-token', 'test-idempotency-key');

    expect($result)->toBeTrue();
});

test('returns false when captcha is not a non-empty string', function (mixed $captcha): void {
    Log::shouldReceive('log')->never();

    Http::fake([
        'challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true], 200),
    ]);

    $result = resolve(ValidateCaptcha::class)->handle($captcha, 'test-idempotency-key');

    expect($result)->toBeFalse();

    Http::assertNothingSent();
})->with([
    'empty string' => [''],
    'null'         => [null],
    'non-string'   => [['nested']],
]);

test('returns false and logs error when Cloudflare Turnstile request throws connection exception', function (): void {
    Log::shouldReceive('log')
        ->once()
        ->withArgs(fn (string $level, string $message, array $context): bool => $level === 'error'
            && $message === 'External - Validate Turnstile - connection failed'
            && ($context['context']['captchaUrl'] ?? '') === 'https://challenges.cloudflare.com/turnstile/v0/siteverify'
            && array_key_exists('exception_message', $context));

    Http::fake([
        'challenges.cloudflare.com/turnstile/v0/siteverify' => Http::failedConnection(),
    ]);

    $result = resolve(ValidateCaptcha::class)->handle('valid-token', 'test-idempotency-key');

    expect($result)->toBeFalse();
});

test('returns false and logs error when Cloudflare Turnstile returns success false', function (): void {
    Log::shouldReceive('log')
        ->once()
        ->withArgs(fn (string $level, string $message, array $context): bool => $level === 'error'
            && $message === 'External - Validate Turnstile - verification failed'
            && ($context['captchaUrl'] ?? '') === 'https://challenges.cloudflare.com/turnstile/v0/siteverify'
            && ($context['response'] ?? null) === ['success' => false]);

    Http::fake([
        'challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => false], 200),
    ]);

    $result = resolve(ValidateCaptcha::class)->handle('invalid-token', 'test-idempotency-key');

    expect($result)->toBeFalse();
});

test('returns false and logs error when Cloudflare Turnstile response is missing success key', function (): void {
    Log::shouldReceive('log')
        ->once()
        ->withArgs(fn (string $level, string $message, array $context): bool => $level === 'error'
            && $message === 'External - Validate Turnstile - verification failed'
            && ($context['captchaUrl'] ?? '') === 'https://challenges.cloudflare.com/turnstile/v0/siteverify'
            && ($context['response'] ?? null) === []);

    Http::fake([
        'challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response([], 200),
    ]);

    $result = resolve(ValidateCaptcha::class)->handle('token', 'test-idempotency-key');

    expect($result)->toBeFalse();
});

test('sends correct payload to Cloudflare Turnstile API including idempotency key', function (): void {
    Log::shouldReceive('log')->never();

    Http::fake([
        'challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true], 200),
    ]);

    resolve(ValidateCaptcha::class)->handle('test-response-token', 'test-idempotency-key');

    Http::assertSent(function ($request): bool {
        $body = $request->data();

        return $request->url() === 'https://challenges.cloudflare.com/turnstile/v0/siteverify'
            && $body['secret'] === 'test-secret-key'
            && $body['response'] === 'test-response-token'
            && $body['idempotency_key'] === 'test-idempotency-key';
    });
});

test('omits idempotency key from payload when not provided', function (): void {
    Log::shouldReceive('log')->never();

    Http::fake([
        'challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true], 200),
    ]);

    resolve(ValidateCaptcha::class)->handle('test-response-token', null);

    Http::assertSent(function ($request): bool {
        $body = $request->data();

        return ! array_key_exists('idempotency_key', $body);
    });
});
