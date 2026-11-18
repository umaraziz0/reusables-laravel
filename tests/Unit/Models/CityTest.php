<?php

declare(strict_types=1);

use App\Models\City;
use App\Models\Province;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->use(RefreshDatabase::class);

test('province relation resolves the province by province code', function (): void {
    $province = Province::query()->create([
        'code' => '11',
        'name' => 'ACEH',
    ]);

    $city = City::query()->create([
        'code'          => '1101',
        'province_code' => $province->code,
        'name'          => 'KABUPATEN ACEH SELATAN',
    ]);

    expect($city->province->is($province))->toBeTrue();
});

test('province name attribute resolves through province relation', function (): void {
    $province = Province::query()->create([
        'code' => '11',
        'name' => 'ACEH',
    ]);

    $city = City::query()->create([
        'code'          => '1101',
        'province_code' => $province->code,
        'name'          => 'KABUPATEN ACEH SELATAN',
    ]);

    expect($city->province_name)->toBe('ACEH');
});
