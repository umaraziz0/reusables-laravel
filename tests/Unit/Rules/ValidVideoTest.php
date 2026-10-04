<?php

declare(strict_types=1);

use App\Rules\ValidVideo;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Validator;

function validateVideo(mixed $video): Illuminate\Validation\Validator
{
    return Validator::make(
        ['video' => $video],
        ['video' => [new ValidVideo]],
    );
}

test('passes for allowed video types under the size limit', function (string $filename, string $mime): void {
    $file = UploadedFile::fake()->create($filename, 100, $mime);

    expect(validateVideo($file)->passes())->toBeTrue();
})->with([
    'mp4'  => ['clip.mp4', 'video/mp4'],
    'webm' => ['clip.webm', 'video/webm'],
    'mov'  => ['clip.mov', 'video/quicktime'],
]);

test('fails when the file exceeds the max size', function (): void {
    $file = UploadedFile::fake()->create('clip.mp4', 20_480, 'video/mp4');

    $validator = validateVideo($file);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('video'))->toBeTrue();
});

test('fails for disallowed file types', function (string $filename, string $mime): void {
    $file = UploadedFile::fake()->create($filename, 100, $mime);

    $validator = validateVideo($file);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('video'))->toBeTrue();
})->with([
    'pdf' => ['document.pdf', 'application/pdf'],
    'avi' => ['clip.avi', 'video/x-msvideo'],
    'png' => ['photo.png', 'image/png'],
]);

test('fails for non-file values', function (mixed $value): void {
    $validator = validateVideo($value);

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('video'))->toBeTrue();
})->with([
    'string' => ['not-a-file'],
]);

test('reads max_filesize from media config', function (): void {
    Config::set('media.video.max_filesize', 50);

    $file = UploadedFile::fake()->create('clip.mp4', 100, 'video/mp4');

    expect(validateVideo($file)->fails())->toBeTrue();
});

test('reads mimes from media config', function (): void {
    Config::set('media.video.mimes', ['webm']);

    $webm = UploadedFile::fake()->create('clip.webm', 100, 'video/webm');
    $mp4  = UploadedFile::fake()->create('clip.mp4', 100, 'video/mp4');

    expect(validateVideo($webm)->passes())->toBeTrue()
        ->and(validateVideo($mp4)->fails())->toBeTrue();
});
