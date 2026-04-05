<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'is_active' => true,
            'expires_at' => null,
        ]);

        User::create([
            'name' => 'Ichal',
            'username' => 'ichal',
            'password' => Hash::make('ichal123'),
            'role' => 'user',
            'is_active' => true,
            'expires_at' => null,
        ]);

        User::create([
            'name' => 'Budi',
            'username' => 'budi',
            'password' => Hash::make('budi123'),
            'role' => 'user',
            'is_active' => true,
            'expires_at' => null,
        ]);
    }
}
