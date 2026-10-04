<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Models\User;

class CreateUser
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data): User
    {
        return User::query()->create([
            'name'         => $data['name'],
            'email'        => $data['email'],
            'password'     => $data['password'],
            'country_code' => $data['country_code'] ?? null,
            'phone_number' => $data['phone_number'] ?? null,
        ]);
    }
}
