<?php

declare(strict_types=1);

use App\Models\City;
use App\Models\Province;
use Illuminate\Foundation\Testing\RefreshDatabase;

pest()->use(RefreshDatabase::class);

test('cities relation keys cities by province code', function (): void {
    $province = Province::query()->create([
        'code' => '11',
        'name' => 'ACEH',
    ]);

    $relation = $province->cities();

    expect($relation->getForeignKeyName())->toBe('province_code')
        ->and($relation->getLocalKeyName())->toBe('code');
});

test('cities relation returns cities linked by province code', function (): void {
    $province = Province::query()->create([
        'code' => '11',
        'name' => 'ACEH',
    ]);

    $firstCity = City::query()->create([
        'code'          => '1101',
        'province_code' => $province->code,
        'name'          => 'KABUPATEN ACEH SELATAN',
    ]);

    $secondCity = City::query()->create([
        'code'          => '1102',
        'province_code' => $province->code,
        'name'          => 'KABUPATEN ACEH TENGGARA',
    ]);

    Province::query()->create([
        'code' => '12',
        'name' => 'SUMATERA UTARA',
    ]);

    City::query()->create([
        'code'          => '1201',
        'province_code' => '12',
        'name'          => 'KABUPATEN TAPANULI TENGAH',
    ]);

    $cities = $province->cities;

    expect($cities)->toHaveCount(2)
        ->and($cities->pluck('id')->all())->toEqual([$firstCity->id, $secondCity->id]);
});
