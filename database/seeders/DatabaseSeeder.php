<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\App;

class DatabaseSeeder extends Seeder
{
    public const string SUPERADMIN_EMAIL = 'superadmin@example.com';

    public const string USER_EMAIL = 'test@example.com';

    /**
     * Seed the application's database.
     *
     * Roles are seeded in every environment. Demo users have the UserFactory default
     * credentials, so they are only seeded locally and in tests, never on a reachable server.
     */
    public function run(): void
    {
        $this->call(RoleSeeder::class);

        if (! App::environment(['local', 'testing'])) {
            return;
        }

        $this->findOrCreateUser('Superadmin', self::SUPERADMIN_EMAIL)
            ->assignRole(Role::SUPERADMIN->value);

        $this->findOrCreateUser('Test User', self::USER_EMAIL);
    }

    private function findOrCreateUser(string $name, string $email): User
    {
        return User::query()->where('email', $email)->first()
            ?? User::factory()->create(['name' => $name, 'email' => $email]);
    }
}
