<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::query()->updateOrCreate(
            ['login' => env('ADMIN_LOGIN', 'admin')],
            [
                'name' => 'Admin Arena Gym',
                'email' => 'admin@arena-gym.local',
                'role' => 'admin',
                'password' => Hash::make(env('ADMIN_PASSWORD', 'admin123')),
            ]
        );

        User::query()->updateOrCreate(
            ['login' => env('MASTER_ADMIN_LOGIN', 'masteradmin')],
            [
                'name' => 'Master Admin Arena Gym',
                'email' => 'masteradmin@arena-gym.local',
                'role' => 'master_admin',
                'password' => Hash::make(env('MASTER_ADMIN_PASSWORD', 'master123')),
            ]
        );

        User::query()->updateOrCreate(
            ['role' => 'cashier'],
            [
                'login' => env('CASHIER_LOGIN', 'kasir'),
                'name' => 'Kasir Arena Gym',
                'email' => 'cashier@arena-gym.local',
                'password' => Hash::make(env('CASHIER_PASSWORD', 'kasir123')),
            ]
        );
    }
}
