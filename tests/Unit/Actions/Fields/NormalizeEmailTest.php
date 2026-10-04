<?php

declare(strict_types=1);

use App\Actions\Fields\NormalizeEmail;

test('trims and lowercases email addresses', function (string $value, string $expected): void {
    expect(new NormalizeEmail()->handle($value))->toBe($expected);
})->with([
    'ascii email'   => ['  JANE@EXAMPLE.COM  ', 'jane@example.com'],
    'unicode email' => ["\u{00A0}ÉLODIE@EXAMPLE.COM\u{00A0}", 'élodie@example.com'],
]);
