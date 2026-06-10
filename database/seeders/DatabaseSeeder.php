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
            'name'     => 'SPV Penjadwalan',
            'email'    => 'spv@gmail.com',
            'password' => Hash::make('123'),
            'role'     => 'spv',
        ]);

        User::create([
            'name'     => 'Asisten Laboratorium',
            'email'    => 'asisten@gmail.com',
            'password' => Hash::make('123'),
            'role'     => 'asisten',
        ]);

        User::create([
            'name'     => 'BEM / Ormawa',
            'email'    => 'ormawa@gmail.com',
            'password' => Hash::make('123'),
            'role'     => 'ormawa',
        ]);

        User::create([
            'name'     => 'Dosen Pengajar',
            'email'    => 'dosen@gmail.com',
            'password' => Hash::make('123'),
            'role'     => 'dosen',
        ]);

        $this->call([
            LabSeeder::class,
        ]);
    }
}