<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Lang;

test('translations load from the root lang directory', function (): void {
    // A `resources/lang` directory, if one exists, makes Laravel ignore `lang/`.
    expect(lang_path())->toBe(base_path('lang'))
        ->and(Lang::has('validation.captcha'))->toBeTrue();
});
