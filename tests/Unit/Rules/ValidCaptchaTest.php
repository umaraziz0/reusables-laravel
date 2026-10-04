<?php

declare(strict_types=1);

use App\Rules\ValidCaptcha;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

beforeEach(function (): void {
    Config::set('services.turnstile.secret_key', 'test-secret-key');
});

test('passes when Cloudflare Turnstile returns success', function (): void {
    Http::fake([
        'challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true], 200),
    ]);

    $validator = Validator::make(
        ['captcha' => 'valid-token'],
        ['captcha' => [new ValidCaptcha]],
    );

    expect($validator->passes())->toBeTrue();
});

test('translates failed captcha validation into a validation error', function (): void {
    Http::fake([
        'challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => false], 200),
    ]);

    $validator = Validator::make(
        ['captcha' => 'invalid-token'],
        ['captcha' => [new ValidCaptcha]],
    );

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('captcha'))->toBe(__('validation.captcha'));
});

test('forwards the request idempotency key to captcha validation', function (): void {
    request()->merge(['idempotency_key' => 'test-idempotency-key']);

    Http::fake([
        'challenges.cloudflare.com/turnstile/v0/siteverify' => Http::response(['success' => true], 200),
    ]);

    $data = [
        'captcha'         => 'test-response-token',
        'idempotency_key' => 'test-idempotency-key',
    ];

    $validator = Validator::make(
        $data,
        ['captcha' => [new ValidCaptcha]],
    );

    $validator->passes();

    Http::assertSent(function ($request): bool {
        $body = $request->data();

        return $body['idempotency_key'] === 'test-idempotency-key';
    });
});
