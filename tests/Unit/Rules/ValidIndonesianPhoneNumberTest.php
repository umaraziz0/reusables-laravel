<?php

declare(strict_types=1);

use App\Rules\ValidIndonesianPhoneNumber;
use Illuminate\Support\Facades\Validator;

function validateIndonesianPhoneNumber(mixed $phoneNumber): Illuminate\Validation\Validator
{
    return Validator::make(
        ['phone_number' => $phoneNumber],
        ['phone_number' => [new ValidIndonesianPhoneNumber]],
    );
}

test('passes for valid local Indonesian phone numbers without country code', function (string $phoneNumber): void {
    $validator = validateIndonesianPhoneNumber($phoneNumber);

    expect($validator->passes())->toBeTrue();
})->with([
    'minimum length' => ['812345678'],
    'typical mobile' => ['8123456789'],
    'maximum length' => ['812345678901'],
]);

test('fails when value contains special characters', function (string $phoneNumber): void {
    $validator = validateIndonesianPhoneNumber($phoneNumber);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('phone_number'))
        ->toBe(__('validation.no_special_characters', ['attribute' => 'phone number']));
})->with([
    'plus prefix' => ['+62812345678'],
    'hyphenated'  => ['812-345-678'],
]);

test('fails when value contains spaces', function (string $phoneNumber): void {
    $validator = validateIndonesianPhoneNumber($phoneNumber);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('phone_number'))
        ->toBe(__('validation.no_spaces', ['attribute' => 'phone number']));
})->with([
    'internal space' => ['812 345678'],
]);

test('fails when value is not numeric', function (): void {
    $validator = validateIndonesianPhoneNumber('812abc5678');

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('phone_number'))
        ->toBe(__('validation.numeric', ['attribute' => 'phone number']));
});

test('fails when value starts with a leading zero', function (string $phoneNumber): void {
    $validator = validateIndonesianPhoneNumber($phoneNumber);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('phone_number'))
        ->toBe(__('validation.no_leading_zeroes', ['attribute' => 'phone number']));
})->with([
    'local format' => ['0812345678'],
]);

test('fails when value is shorter than the minimum length', function (): void {
    $validator = validateIndonesianPhoneNumber('81234567');

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('phone_number'))
        ->toBe(__('validation.min.string', ['attribute' => 'phone number', 'min' => 9]));
});

test('fails when value is longer than the maximum length', function (): void {
    $validator = validateIndonesianPhoneNumber('8123456789012');

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('phone_number'))
        ->toBe(__('validation.max.string', ['attribute' => 'phone number', 'max' => 12]));
});
