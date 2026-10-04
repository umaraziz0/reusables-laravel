<?php

declare(strict_types=1);

namespace App\Observers;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class UserObserver
{
    /**
     * Handle the User "creating" event.
     */
    public function creating(User $user): void
    {
        if (empty($user->uuid)) {
            $user->uuid = (string) Str::uuid();
        }
    }

    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        Cache::tags(User::CACHE_TAG_LIST)->flush();
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        if ($user->wasChanged()) {
            if ($user->uuid !== null) {
                $cacheKey = User::getCacheKeyForUuid($user->uuid);

                Cache::tags(User::CACHE_TAG_ITEM)->forget($cacheKey);
            }

            Cache::tags(User::CACHE_TAG_LIST)->flush();
        }
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        if ($user->uuid !== null) {
            $cacheKey = User::getCacheKeyForUuid($user->uuid);

            Cache::tags(User::CACHE_TAG_ITEM)->forget($cacheKey);
        }

        Cache::tags(User::CACHE_TAG_LIST)->flush();
    }
}
