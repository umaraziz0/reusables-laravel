<?php

declare(strict_types=1);

use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

beforeEach(function (): void {
    Route::get('/api/bootstrap-validation-exception', function (): never {
        throw ValidationException::withMessages([
            'email' => ['Email is required.'],
        ]);
    });

    Route::middleware('web')->get('/bootstrap-validation-exception', function (): never {
        throw ValidationException::withMessages([
            'email' => ['Email is required.'],
        ]);
    });

    Route::get('/api/bootstrap-throttle-exception', function (): never {
        throw new ThrottleRequestsException('Too many requests.', headers: [
            'Retry-After'           => '60',
            'X-RateLimit-Remaining' => '0',
        ]);
    });

    Route::get('/bootstrap-throttle-exception', function (): never {
        throw new ThrottleRequestsException('Too many requests.', headers: [
            'Retry-After'           => '60',
            'X-RateLimit-Remaining' => '0',
        ]);
    });
});

test('api validation exceptions use custom JSON response', function (): void {
    $this->getJson('/api/bootstrap-validation-exception')
        ->assertUnprocessable()
        ->assertHeader('Content-Type', 'application/json')
        ->assertExactJson([
            'success' => false,
            'message' => 'Email is required.',
            'data'    => null,
            'errors'  => [
                'email' => ['Email is required.'],
            ],
        ]);
});

test('non-api validation exceptions use default rendering', function (): void {
    $this->get('/bootstrap-validation-exception')
        ->assertRedirect()
        ->assertSessionHasErrors([
            'email' => 'Email is required.',
        ]);
});

test('api throttle exceptions use custom JSON response and preserve headers', function (): void {
    $this->getJson('/api/bootstrap-throttle-exception')
        ->assertTooManyRequests()
        ->assertHeader('Content-Type', 'application/json')
        ->assertHeader('Retry-After', '60')
        ->assertHeader('X-RateLimit-Remaining', '0')
        ->assertExactJson([
            'success' => false,
            'message' => 'Too many requests.',
            'data'    => null,
        ]);
});

test('non-api throttle exceptions use default rendering', function (): void {
    $response = $this->get('/bootstrap-throttle-exception');

    $response->assertStatus(429);
    expect($response->headers->get('Content-Type'))->toStartWith('text/html');
});
