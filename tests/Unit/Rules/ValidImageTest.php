<?php

declare(strict_types=1);

use App\Rules\ValidImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

function validateImage(mixed $image): Illuminate\Validation\Validator
{
    return Validator::make(
        ['image' => $image],
        ['image' => [new ValidImage]],
    );
}

test('passes for allowed image types under the size limit', function (string $filename): void {
    $file = UploadedFile::fake()->image($filename);

    expect(validateImage($file)->passes())->toBeTrue();
})->with([
    'jpg'  => ['photo.jpg'],
    'jpeg' => ['photo.jpeg'],
    'png'  => ['photo.png'],
    'webp' => ['photo.webp'],
    'avif' => ['photo.avif'],
]);

test('fails when the file exceeds the max size', function (): void {
    $file = UploadedFile::fake()->image('photo.jpg')->size(2048);

    $validator = validateImage($file);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('image'))->toBeTrue();
});

test('fails for disallowed file types', function (string $filename, string $mime): void {
    $file = UploadedFile::fake()->create($filename, 100, $mime);

    $validator = validateImage($file);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('image'))->toBeTrue();
})->with([
    'pdf' => ['document.pdf', 'application/pdf'],
    'svg' => ['icon.svg', 'image/svg+xml'],
    'gif' => ['anim.gif', 'image/gif'],
]);

test('fails for non-file values', function (mixed $value): void {
    $validator = validateImage($value);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('image'))->toBeTrue();
})->with([
    'string' => ['not-a-file'],
]);

test('reads max_filesize from media config', function (): void {
    Config::set('media.image.max_filesize', 50);

    $file = UploadedFile::fake()->image('photo.jpg')->size(100);

    expect(validateImage($file)->fails())->toBeTrue();
});

test('reads mimes from media config', function (): void {
    Config::set('media.image.mimes', ['png']);

    $png = UploadedFile::fake()->image('photo.png');
    $jpg = UploadedFile::fake()->image('photo.jpg');

    expect(validateImage($png)->passes())->toBeTrue()
        ->and(validateImage($jpg)->fails())->toBeTrue();
});
