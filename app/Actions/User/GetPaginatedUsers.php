<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;

class GetPaginatedUsers
{
    /**
     * @return LengthAwarePaginator<int, User>
     */
    public function handle(?int $page = null, int $perPage = 10): LengthAwarePaginator
    {
        $page ??= Paginator::resolveCurrentPage();

        $cacheKey = User::CACHE_TAG_LIST . ':paginated:page:' . $page . ':per_page:' . $perPage;

        return Cache::tags(User::CACHE_TAG_LIST)
            ->remember(
                key: $cacheKey,
                ttl: Config::integer('cache.ttl.five_minutes'),
                callback: fn (): LengthAwarePaginator => User::query()
                    ->select(User::CACHE_ATTRIBUTES)
                    ->orderBy('id')
                    ->paginate(perPage: $perPage, page: $page)
            );
    }
}
