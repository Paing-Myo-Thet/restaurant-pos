<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;


class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Restaurant Admin',
            'email' => 'admin@restaurant.com',
            'password' => 'password',
            'role_id' => Role::where('is_system', true)->value('id'),
        ]);
    }
}