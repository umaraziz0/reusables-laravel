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

test('applies type and dimension checks to a real avif image', function (): void {
    $file = new UploadedFile(__DIR__ . '/Fixtures/photo-64x64.avif', 'photo.avif', 'image/avif', null, true);

    expect(validateImage($file)->passes())->toBeTrue();

    Config::set('media.image.max_width', 63);

    expect(validateImage($file)->fails())->toBeTrue();
});

test('fails for non-file values', function (mixed $value): void {
    $validator = validateImage($value);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('image'))->toBeTrue();
})->with([
    'string' => ['not-a-file'],
]);

test('fails when the image exceeds the max dimensions while under the size limit', function (int $width, int $height): void {
    $file = UploadedFile::fake()->image('photo.jpg', $width, $height);

    $validator = validateImage($file);

    expect($file->getSize())->toBeLessThan(1024 * 1024)
        ->and($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('image'))->toBeTrue();
})->with([
    'too wide' => [4097, 10],
    'too tall' => [10, 4097],
]);

test('passes when the image is exactly at the max width and height', function (string $filename, int $width, int $height): void {
    $file = UploadedFile::fake()->image($filename, $width, $height);

    expect(validateImage($file)->passes())->toBeTrue();
})->with([
    'max width'  => ['wide.png', 4096, 10],
    'max height' => ['tall.png', 10, 4096],
]);

test('reads max_width and max_height from media config', function (): void {
    Config::set('media.image.max_width', 100);
    Config::set('media.image.max_height', 50);

    $fits    = UploadedFile::fake()->image('fits.png', 100, 50);
    $tooWide = UploadedFile::fake()->image('wide.png', 101, 50);
    $tooTall = UploadedFile::fake()->image('tall.png', 100, 51);

    expect(validateImage($fits)->passes())->toBeTrue()
        ->and(validateImage($tooWide)->fails())->toBeTrue()
        ->and(validateImage($tooTall)->fails())->toBeTrue();
});

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
