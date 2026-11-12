<?php

declare(strict_types=1);

use App\Data\CountryCodes;

test('every country has a unique ISO code and a well-formed dial code', function (): void {
    $countries = CountryCodes::all();
    $codes     = array_column($countries, 'code');

    expect($countries)->not->toBeEmpty()
        ->and($codes)->toHaveSameSize(array_unique($codes))
        ->and($codes)->each->toMatch('/^[A-Z]{2}$/')
        ->and(array_column($countries, 'dial_code'))->each->toMatch('/^\+[0-9]+$/')
        ->and(array_column($countries, 'name'))->each->not->toBeEmpty();
});
