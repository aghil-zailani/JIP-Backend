<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // Buat akun Admin contoh
        User::create([
            'name' => 'Admin JIM',
            'email' => 'admin@jim.id',
            'password' => Hash::make('123'),
            'role' => 'admin',
            'no_hp' => '08123456789',
        ]);

        User::create([
            'name' => 'Budi Inspektur',
            'email' => 'budi@jim.id',
            'password' => Hash::make('123'),
            'role' => 'inspektor',
            'no_hp' => '08987654321',
        ]);

        User::create([
            'name' => 'Bayu Biru',
            'email' => 'bayu@jim.id',
            'password' => Hash::make('123'),
            'role' => 'pelanggan',
            'no_hp' => '08234567890',
        ]);
    }
}