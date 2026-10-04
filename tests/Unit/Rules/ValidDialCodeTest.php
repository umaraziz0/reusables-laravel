<?php

declare(strict_types=1);

use App\Data\CountryCodes;
use App\Rules\ValidDialCode;
use Illuminate\Support\Facades\Validator;

function validateDialCode(mixed $dialCode): Illuminate\Validation\Validator
{
    return Validator::make(
        ['country_code' => $dialCode],
        ['country_code' => [new ValidDialCode]],
    );
}

test('passes for valid dial codes from the country list', function (string $dialCode): void {
    $validator = validateDialCode($dialCode);

    expect($validator->passes())->toBeTrue();
})->with([
    'indonesia'      => ['+62'],
    'united states'  => ['+1'],
    'cayman islands' => ['+1345'],
]);

test('fails for dial codes not in the country list', function (string $dialCode): void {
    $validator = validateDialCode($dialCode);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('country_code'))
        ->toBe(__('validation.exists', ['attribute' => 'country_code']));
})->with([
    'without plus prefix' => ['62'],
    'invalid code'        => ['+99999'],
    'letters'             => ['+abc'],
    'partial match'       => ['+6'],
    'malformed spacing'   => ['+ 345'],
]);

test('fails when value is empty and the field is required', function (mixed $dialCode): void {
    $validator = Validator::make(
        ['country_code' => $dialCode],
        ['country_code' => ['required', new ValidDialCode]],
    );

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('country_code'))->toBeTrue();
})->with([
    'empty string' => [''],
    'null'         => [null],
]);

test('isValidDialCode returns true only for known dial codes', function (): void {
    expect(CountryCodes::isValidDialCode('+62'))->toBeTrue()
        ->and(CountryCodes::isValidDialCode('+1'))->toBeTrue()
        ->and(CountryCodes::isValidDialCode('+1345'))->toBeTrue()
        ->and(CountryCodes::isValidDialCode('+999'))->toBeFalse();
});
