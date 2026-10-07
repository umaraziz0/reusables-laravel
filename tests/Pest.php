<?php

declare(strict_types=1);

use App\Enums\Ability\UserAbility;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

pest()->tia()
    ->baselined()
    ->filtered();

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->in('Feature');

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(fn () => $this->withVite()) // the browser needs the built assets TestCase disables
    ->in('Browser');

pest()->extend(TestCase::class)->in('Unit');

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Plain-text password that satisfies configured Password::defaults() rules
 * (mixed case, letters, numbers, symbols, uncompromised).
 */
function validTestPassword(): string
{
    return 'Zx7!kQm9@pL2#vR';
}

/**
 * Authenticate a request to the private API with Sanctum and a whitelisted IP.
 *
 * @param  array<int, UserAbility>  $abilities
 */
function authenticatePrivateApi(array $abilities = [UserAbility::View]): User
{
    config(['api.allowed_ips' => ['127.0.0.1']]);

    $user = User::factory()->create();
    Sanctum::actingAs($user, array_map(
        fn (UserAbility $ability): string => $ability->value,
        $abilities,
    ));

    return $user;
}
