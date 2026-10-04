<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Laravolt\Indonesia\Models\City as LaravoltCity;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Village;
use Override;

/**
 * @property string $id
 * @property string $code
 * @property string $province_code
 * @property string $name
 * @property array<array-key, mixed>|null $meta
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read Collection<int, District> $districts
 * @property-read int|null $districts_count
 * @property-read mixed $logo_path
 * @property-read mixed $province_name
 * @property-read Province $province
 * @property-read Collection<int, Village> $villages
 * @property-read int|null $villages_count
 *
 * @method static Builder<static>|City newModelQuery()
 * @method static Builder<static>|City newQuery()
 * @method static Builder<static>|City query()
 * @method static Builder<static>|City search($keyword)
 * @method static Builder<static>|City whereCode($value)
 * @method static Builder<static>|City whereCreatedAt($value)
 * @method static Builder<static>|City whereId($value)
 * @method static Builder<static>|City whereMeta($value)
 * @method static Builder<static>|City whereName($value)
 * @method static Builder<static>|City whereProvinceCode($value)
 * @method static Builder<static>|City whereUpdatedAt($value)
 *
 * @mixin Model
 */
class City extends LaravoltCity
{
    /**
     * Get the province that the city belongs to.
     *
     * @return BelongsTo<Province, $this>
     */
    #[Override]
    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class, 'province_code', 'code');
    }
}
