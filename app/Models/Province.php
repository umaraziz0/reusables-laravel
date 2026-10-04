<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravolt\Indonesia\Models\District;
use Laravolt\Indonesia\Models\Province as LaravoltProvince;
use Override;

/**
 * @property string $id
 * @property string $code
 * @property string $name
 * @property array<array-key, mixed>|null $meta
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property-read Collection<int, City> $cities
 * @property-read int|null $cities_count
 * @property-read Collection<int, District> $districts
 * @property-read int|null $districts_count
 * @property-read mixed $logo_path
 *
 * @method static Builder<static>|Province newModelQuery()
 * @method static Builder<static>|Province newQuery()
 * @method static Builder<static>|Province query()
 * @method static Builder<static>|Province search($keyword)
 * @method static Builder<static>|Province whereCode($value)
 * @method static Builder<static>|Province whereCreatedAt($value)
 * @method static Builder<static>|Province whereId($value)
 * @method static Builder<static>|Province whereMeta($value)
 * @method static Builder<static>|Province whereName($value)
 * @method static Builder<static>|Province whereUpdatedAt($value)
 *
 * @mixin Model
 */
class Province extends LaravoltProvince
{
    /**
     * Get the cities for the province.
     *
     * @return HasMany<City, $this>
     */
    #[Override]
    public function cities(): HasMany
    {
        return $this->hasMany(City::class, 'province_code', 'code');
    }
}
