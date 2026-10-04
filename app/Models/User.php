<?php

declare(strict_types=1);

namespace App\Models;

use App\Database\Expressions\UserSearchVector;
use App\Observers\UserObserver;
use Carbon\CarbonImmutable;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Laravel\Fortify\Contracts\PasskeyUser;
use Laravel\Fortify\PasskeyAuthenticatable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Passkeys\Passkey;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Sanctum\PersonalAccessToken;
use Override;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $country_code
 * @property string|null $phone_number
 * @property CarbonImmutable|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property CarbonImmutable|null $created_at
 * @property CarbonImmutable|null $updated_at
 * @property string|null $two_factor_secret
 * @property string|null $two_factor_recovery_codes
 * @property CarbonImmutable|null $two_factor_confirmed_at
 * @property string|null $uuid
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, Passkey> $passkeys
 * @property-read int|null $passkeys_count
 * @property-read Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read Collection<int, Role> $roles
 * @property-read int|null $roles_count
 * @property-read Collection<int, Permission> $teams
 * @property-read int|null $teams_count
 * @property-read Collection<int, PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 *
 * @method static UserFactory factory($count = null, $state = [])
 * @method static Builder<static>|User filterByKeyword(?string $keyword)
 * @method static Builder<static>|User newModelQuery()
 * @method static Builder<static>|User newQuery()
 * @method static Builder<static>|User permission($permissions, bool $without = false)
 * @method static Builder<static>|User query()
 * @method static Builder<static>|User role($roles, ?string $guard = null, bool $without = false)
 * @method static Builder<static>|User team($teams, bool $without = false)
 * @method static Builder<static>|User whereCountryCode($value)
 * @method static Builder<static>|User whereCreatedAt($value)
 * @method static Builder<static>|User whereEmail($value)
 * @method static Builder<static>|User whereEmailVerifiedAt($value)
 * @method static Builder<static>|User whereId($value)
 * @method static Builder<static>|User whereName($value)
 * @method static Builder<static>|User wherePassword($value)
 * @method static Builder<static>|User wherePhoneNumber($value)
 * @method static Builder<static>|User whereRememberToken($value)
 * @method static Builder<static>|User whereTwoFactorConfirmedAt($value)
 * @method static Builder<static>|User whereTwoFactorRecoveryCodes($value)
 * @method static Builder<static>|User whereTwoFactorSecret($value)
 * @method static Builder<static>|User whereUpdatedAt($value)
 * @method static Builder<static>|User whereUuid($value)
 * @method static Builder<static>|User withoutPermission($permissions)
 * @method static Builder<static>|User withoutRole($roles, ?string $guard = null)
 * @method static Builder<static>|User withoutTeam($teams)
 *
 * @mixin Model
 */
#[ObservedBy(UserObserver::class)]
#[Fillable([
    'name',
    'email',
    'country_code',
    'phone_number',
    'password',
])]
#[Hidden([
    'password',
    'two_factor_secret',
    'two_factor_recovery_codes',
    'remember_token',
])]
class User extends Authenticatable implements MustVerifyEmail, PasskeyUser
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, HasRoles, Notifiable, PasskeyAuthenticatable, TwoFactorAuthenticatable;

    // --- Cache --- //

    final public const string CACHE_TAG_ITEM = 'users:item';

    final public const string CACHE_TAG_LIST = 'users:list';

    /**
     * The attributes that are safe to be cached.
     *
     * @var list<string>
     */
    final public const array CACHE_ATTRIBUTES = [
        'uuid',
        'name',
        'email',
        'country_code',
        'phone_number',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'password' => 'hashed',

            'email_verified_at'       => 'datetime',
            'two_factor_confirmed_at' => 'datetime',
        ];
    }

    // --- Query Scopes --- //

    /**
     * @param  Builder<User>  $query
     */
    protected function scopeFilterByKeyword(Builder $query, ?string $keyword): void
    {
        if (blank($keyword)) {
            return;
        }

        $driver = DB::connection($query->getModel()->getConnectionName())->getDriverName();

        // Escape special characters (e.g "100%" becomes "100\%" to prevent incorrect results)
        $pattern = '%' . addcslashes($keyword, '%_\\') . '%';

        if ($driver === 'pgsql') {
            $query->whereRaw(UserSearchVector::ILIKE_COMPARISON, [$pattern]);

            return;
        }

        $query->where(function (Builder $query) use ($driver, $pattern): void {
            $combinedPhoneComparison = match ($driver) {
                'sqlite' => "COALESCE(country_code, '') || COALESCE(phone_number, '') LIKE ?",
                default  => "CONCAT(COALESCE(country_code, ''), COALESCE(phone_number, '')) LIKE ?",
            };

            $query->where(function (Builder $query) use ($pattern, $combinedPhoneComparison): void {
                $query->where('name', 'LIKE', $pattern)
                    ->orWhere('email', 'LIKE', $pattern)
                    ->orWhere('phone_number', 'LIKE', $pattern)
                    ->orWhereRaw($combinedPhoneComparison, [$pattern]);
            });
        });
    }

    /**
     * Get the cache key for a user by UUID.
     */
    public static function getCacheKeyForUuid(string $uuid): string
    {
        return self::CACHE_TAG_ITEM . ':uuid:' . $uuid;
    }

    // --- Relationships --- //

    // --- Methods --- //
}
