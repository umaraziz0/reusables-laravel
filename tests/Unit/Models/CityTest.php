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

    $relation = $city->province();

    expect($relation->getForeignKeyName())->toBe('province_code')
        ->and($relation->getOwnerKeyName())->toBe('code')
        ->and($city->province->is($province))->toBeTrue()
        ->and($city->province_code)->toBe($province->code);
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
