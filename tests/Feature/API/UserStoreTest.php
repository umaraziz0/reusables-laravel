<?php

declare(strict_types=1);

use App\Actions\User\CreateUser;
use App\Enums\Ability\UserAbility;
use App\Jobs\LogActivity as LogActivityJob;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Queue;

/**
 * @param  array<string, string>  $overrides
 * @return array<string, string>
 */
function validUserPayload(array $overrides = []): array
{
    return array_merge([
        'name'                  => 'Jane Doe',
        'email'                 => 'jane@example.com',
        'password'              => validTestPassword(),
        'password_confirmation' => validTestPassword(),
    ], $overrides);
}

beforeEach(function (): void {
    Http::fake([
        'api.pwnedpasswords.com/range/*' => Http::response('', 200),
    ]);
});

test('denies unauthenticated requests', function (): void {
    config(['api.allowed_ips' => ['127.0.0.1']]);

    $this->postJson(route('api.users.store'), validUserPayload())
        ->assertUnauthorized();
});

test('logs validation failure and redacts failed fields from request context', function (): void {
    authenticatePrivateApi([UserAbility::Create]);
    $log = Log::spy();

    $this->postJson(route('api.users.store'), validUserPayload([
        'email' => 'not-an-email',
    ]))->assertUnprocessable();

    $log->shouldHaveReceived('log')
        ->once()
        ->with(
            'error',
            'API - Create User - validation failed',
            Mockery::on(fn (array $context): bool => isset($context['errors']['email'])
                && ! array_key_exists('email', $context['request'])
                && ($context['request']['name'] ?? null) === 'J*** D**'
                && ! array_key_exists('password', $context['request'])
                && ! array_key_exists('password_confirmation', $context['request']))
        );
});

test('does not log validation failure on successful create', function (): void {
    $authenticatedUser = authenticatePrivateApi([UserAbility::Create]);
    $log               = Log::spy();
    Queue::fake([LogActivityJob::class]);

    $response = $this->postJson(route('api.users.store'), validUserPayload());

    $response->assertCreated();

    $log->shouldHaveReceived('log')
        ->once()
        ->with('info', 'API - Create User - user created', Mockery::on(fn (array $context): bool => isset($context['user_uuid'])
            && ($context['email'] ?? null) === 'j**e@*******.com'));

    $log->shouldNotHaveReceived('log', [
        'error',
        'API - Create User - validation failed',
        Mockery::any(),
    ]);

    Queue::assertPushed(LogActivityJob::class, fn (LogActivityJob $job): bool => $job->module === 'API'
        && $job->action === 'Create User'
        && $job->message === 'user created'
        && $job->performedOn instanceof User
        && $job->performedOn->email === 'jane@example.com'
        && $job->causedBy?->is($authenticatedUser)
        && $job->withProperties === []
        && $job->attributeChanges === []
    );
});

test('logs a redacted email and returns a server error when creation fails', function (): void {
    authenticatePrivateApi([UserAbility::Create]);
    $log = Log::spy();
    Queue::fake([LogActivityJob::class]);
    $this->mock(CreateUser::class)
        ->shouldReceive('handle')
        ->once()
        ->andThrow(new RuntimeException('creation failed'));

    $response = $this->postJson(route('api.users.store'), validUserPayload());

    $response
        ->assertInternalServerError()
        ->assertJsonPath('success', false)
        ->assertJsonPath('message', 'Failed to create user');

    $this->assertDatabaseMissing('users', ['email' => 'jane@example.com']);

    $log->shouldHaveReceived('log')
        ->once()
        ->with(
            'error',
            'API - Create User - failed to create user',
            Mockery::on(fn (array $context): bool => ($context['context']['email'] ?? null) === 'j**e@*******.com'
                && ! str_contains(json_encode($context, JSON_THROW_ON_ERROR), 'jane@example.com')),
        );

    Queue::assertNotPushed(LogActivityJob::class);
});

test('returns validation errors when required fields are missing', function (): void {
    authenticatePrivateApi([UserAbility::Create]);
    $response = $this->postJson(route('api.users.store'), []);

    $response
        ->assertUnprocessable()
        ->assertJson([
            'success' => false,
            'data'    => null,
        ])
        ->assertJsonStructure([
            'success',
            'message',
            'data',
            'errors' => [
                'name',
                'email',
                'password',
            ],
        ]);
});

test('returns validation errors for invalid email', function (): void {
    authenticatePrivateApi([UserAbility::Create]);
    $response = $this->postJson(route('api.users.store'), validUserPayload([
        'email' => 'not-an-email',
    ]));

    $response
        ->assertUnprocessable()
        ->assertJson([
            'success' => false,
        ])
        ->assertJsonValidationErrors(['email']);
});

test('returns validation errors for weak password', function (): void {
    authenticatePrivateApi([UserAbility::Create]);
    $response = $this->postJson(route('api.users.store'), validUserPayload([
        'password'              => 'weak',
        'password_confirmation' => 'weak',
    ]));

    $response
        ->assertUnprocessable()
        ->assertJson([
            'success' => false,
        ])
        ->assertJsonValidationErrors(['password']);
});

test('returns validation errors when password confirmation does not match', function (): void {
    authenticatePrivateApi([UserAbility::Create]);
    $response = $this->postJson(route('api.users.store'), validUserPayload([
        'password_confirmation' => 'DifferentPassword1!',
    ]));

    $response
        ->assertUnprocessable()
        ->assertJson([
            'success' => false,
        ])
        ->assertJsonValidationErrors(['password']);
});

test('returns validation errors for invalid name', function (): void {
    authenticatePrivateApi([UserAbility::Create]);
    $response = $this->postJson(route('api.users.store'), validUserPayload([
        'name' => 'Jane123',
    ]));

    $response
        ->assertUnprocessable()
        ->assertJson([
            'success' => false,
        ])
        ->assertJsonValidationErrors(['name']);
});

test('creates a user with normalized name and email', function (): void {
    authenticatePrivateApi([UserAbility::Create]);

    $response = $this->postJson(route('api.users.store'), validUserPayload([
        'name'  => "  José   O'Connor-Smith  ",
        'email' => '  JOSE@EXAMPLE.COM  ',
    ]));

    $response
        ->assertCreated()
        ->assertJsonPath('data.name', "José O'Connor-Smith")
        ->assertJsonPath('data.email', 'jose@example.com');

    $this->assertDatabaseHas('users', [
        'name'  => "José O'Connor-Smith",
        'email' => 'jose@example.com',
    ]);
});

test('returns 422 when the name contains only whitespace', function (): void {
    authenticatePrivateApi([UserAbility::Create]);

    $this->postJson(route('api.users.store'), validUserPayload([
        'name'  => '   ',
        'email' => 'whitespace@example.com',
    ]))
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['name']);

    $this->assertDatabaseMissing('users', [
        'email' => 'whitespace@example.com',
    ]);
});

test('returns validation errors for invalid country code', function (): void {
    authenticatePrivateApi([UserAbility::Create]);
    $response = $this->postJson(route('api.users.store'), validUserPayload([
        'country_code' => '62',
    ]));

    $response
        ->assertUnprocessable()
        ->assertJson([
            'success' => false,
        ])
        ->assertJsonValidationErrors(['country_code']);
});

test('returns validation errors for invalid phone number', function (): void {
    authenticatePrivateApi([UserAbility::Create]);
    $response = $this->postJson(route('api.users.store'), validUserPayload([
        'phone_number' => '0812345678',
    ]));

    $response
        ->assertUnprocessable()
        ->assertJson([
            'success' => false,
        ])
        ->assertJsonValidationErrors(['phone_number']);
});

test('returns validation errors for duplicate email', function (): void {
    authenticatePrivateApi([UserAbility::Create]);
    User::factory()->create(['email' => 'jane@example.com']);

    $response = $this->postJson(route('api.users.store'), validUserPayload());

    $response
        ->assertUnprocessable()
        ->assertJson([
            'success' => false,
        ])
        ->assertJsonValidationErrors(['email']);
});

test('creates a user successfully', function (): void {
    authenticatePrivateApi([UserAbility::Create]);
    $response = $this->postJson(route('api.users.store'), validUserPayload([
        'country_code' => '+62',
        'phone_number' => '8123456789',
    ]));

    $user = User::query()->where('email', 'jane@example.com')->first();

    $response
        ->assertCreated()
        ->assertJsonStructure([
            'success',
            'message',
            'data' => [
                'uuid',
                'name',
                'email',
                'country_code',
                'phone_number',
                'created_at',
            ],
        ])
        ->assertJson([
            'success' => true,
            'message' => 'User created successfully',
            'data'    => [
                'uuid'  => $user->uuid,
                'name'  => 'Jane Doe',
                'email' => 'jane@example.com',
            ],
        ]);

    $this->assertDatabaseHas('users', [
        'name'         => 'Jane Doe',
        'email'        => 'jane@example.com',
        'country_code' => '+62',
        'phone_number' => '8123456789',
    ]);

    $data = $response->json('data');
    expect($data)->not->toHaveKeys(['password', 'remember_token', 'two_factor_secret', 'two_factor_recovery_codes']);
});
