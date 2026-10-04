<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\Role as RoleEnum;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * * To run: `php artisan db:seed --class=RoleSeeder`
     */
    public function run(): void
    {
        Role::query()->updateOrCreate(['id' => 1], [
            'name'       => RoleEnum::SUPERADMIN->value,
            'guard_name' => 'web',
        ]);

        Role::query()->updateOrCreate(['id' => 2], [
            'name'       => RoleEnum::ADMIN->value,
            'guard_name' => 'web',
        ]);
    }
}
