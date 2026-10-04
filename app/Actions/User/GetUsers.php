<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class GetUsers
{
    /**
     * @return Collection<int, User>
     */
    public function handle(): Collection
    {
        return Cache::tags(User::CACHE_TAG_LIST)
            ->remember(
                key: User::CACHE_TAG_LIST,
                ttl: Config::integer('cache.ttl.five_minutes'),
                callback: fn (): Collection => User::query()
                    ->select(User::CACHE_ATTRIBUTES)
                    ->get()
            );
    }
}
