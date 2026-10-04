<?php

declare(strict_types=1);

use App\Rules\ValidName;
use Illuminate\Support\Facades\Validator;

function validateName(mixed $name): Illuminate\Validation\Validator
{
    return Validator::make(
        ['name' => $name],
        ['name' => [new ValidName]],
    );
}

test('passes for names containing letters, combining marks, spaces, apostrophes, and hyphens', function (string $name): void {
    $validator = validateName($name);

    expect($validator->passes())->toBeTrue();
})->with([
    'single word'           => ['John'],
    'multiple words'        => ['Mary Jane'],
    'unicode letter'        => ['José'],
    'combining mark'        => ["Jose\u{0301}"],
    'apostrophe'            => ["O'Brien"],
    'curly apostrophe'      => ['D’Angelo'],
    'hyphen'                => ['Mary-Jane'],
    'apostrophe and hyphen' => ["O'Connor-Smith"],
]);

test('fails when value contains invalid characters', function (mixed $name): void {
    $validator = validateName($name);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('name'))
        ->toBe(__('validation.alpha', ['attribute' => 'name']));
})->with([
    'letters and digits' => ['John2'],
    'period'             => ['Dr. Smith'],
    'non-string value'   => [123],
]);

test('fails when value contains only whitespace and the field is required', function (string $name): void {
    $validator = Validator::make(
        ['name' => $name],
        ['name' => ['required', new ValidName]],
    );

    expect($validator->fails())->toBeTrue();
})->with([
    'spaces' => ['   '],
    'tabs'   => ["\t\t"],
]);

test('fails when value is null', function (): void {
    $validator = validateName(null);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->first('name'))
        ->toBe(__('validation.alpha', ['attribute' => 'name']));
});
