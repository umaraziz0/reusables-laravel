<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class GetUserByUuid
{
    public function handle(string $uuid): ?User
    {
        $cacheKey = User::getCacheKeyForUuid($uuid);

        return Cache::tags(User::CACHE_TAG_ITEM)
            ->remember(
                key: $cacheKey,
                ttl: Config::integer('cache.ttl.five_minutes'),
                callback: fn (): ?User => User::query()
                    ->select(User::CACHE_ATTRIBUTES)
                    ->where('uuid', $uuid)
                    ->first()
            );
    }
}
