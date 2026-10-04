<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

function validateDefaultPassword(string $password): Illuminate\Validation\Validator
{
    return Validator::make(
        ['password' => $password],
        ['password' => [Password::default()]],
    );
}

function fakeUncompromisedPasswords(): void
{
    Http::fake([
        'api.pwnedpasswords.com/range/*' => Http::response('', 200),
    ]);
}

test('passes when password meets configured defaults', function (): void {
    fakeUncompromisedPasswords();

    $validator = validateDefaultPassword(validTestPassword());

    expect($validator->passes())->toBeTrue();
});

test('fails when password is too short', function (): void {
    fakeUncompromisedPasswords();

    $validator = validateDefaultPassword('Ab1!xYz');

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('password'))
        ->toBe(__('validation.min.string', ['attribute' => 'password', 'min' => 8]));
});

test('fails when password is missing mixed case', function (): void {
    fakeUncompromisedPasswords();

    $validator = validateDefaultPassword('password1!');

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('password'))
        ->toBe(__('validation.password.mixed', ['attribute' => 'password']));
});

test('fails when password is missing letters', function (): void {
    fakeUncompromisedPasswords();

    $validator = validateDefaultPassword('12345678!');

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->get('password'))
        ->toContain(__('validation.password.letters', ['attribute' => 'password']));
});

test('fails when password is missing numbers', function (): void {
    fakeUncompromisedPasswords();

    $validator = validateDefaultPassword('Password!');

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('password'))
        ->toBe(__('validation.password.numbers', ['attribute' => 'password']));
});

test('fails when password is missing symbols', function (): void {
    fakeUncompromisedPasswords();

    $validator = validateDefaultPassword('Password1');

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('password'))
        ->toBe(__('validation.password.symbols', ['attribute' => 'password']));
});

test('fails when password is compromised', function (): void {
    $password   = validTestPassword();
    $hashSuffix = mb_substr(mb_strtoupper(sha1($password)), 5);

    Http::fake([
        'api.pwnedpasswords.com/range/*' => Http::response("{$hashSuffix}:10", 200),
    ]);

    $validator = validateDefaultPassword($password);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('password'))
        ->toBe(__('validation.password.uncompromised', ['attribute' => 'password']));
});
