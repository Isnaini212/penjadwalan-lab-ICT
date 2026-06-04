<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Bikin Akun Master SPV
        User::create([
            'name'     => 'son son apa yg tau',
            'email'    => 'son@gmail.com',
            'password' => Hash::make('123'), // Passwordnya: password123
            'role'     => 'spv',
        ]);
    }
}