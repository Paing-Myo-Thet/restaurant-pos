<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Enums\UserRole;
use App\Models\User;


class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Restaurant Admin',
            'email' => 'admin@restaurant.com',
            'password' => 'password',
            'role' => UserRole::ADMIN,
        ]);
    }
}