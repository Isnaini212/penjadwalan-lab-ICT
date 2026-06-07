<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Lab;

class LabSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lab 2
        Lab::create([
            'nama_lab' => 'Lab 02',
            'kapasitas' => 36,
            'fasilitas' => 'Windows 11, VS Code, MS Office, Chrome',
        ]);


        // Lab 4 
        Lab::create([
            'nama_lab' => 'Lab 04',
            'kapasitas' => 36,
            'fasilitas' => 'Android Studio, Flutter SDK, Java JDK, VS Code (Mobile Dev & FEB)',
        ]);

        // Lab 5
        Lab::create([
            'nama_lab' => 'Lab 05',
            'kapasitas' => 36,
            'fasilitas' => 'Windows 11, VS Code, MS Office, Chrome',
        ]);

        // Lab 6
        Lab::create([
            'nama_lab' => 'Lab 06',
            'kapasitas' => 36,
            'fasilitas' => 'Windows 11, VS Code, MS Office, Chrome',
        ]);

        // Lab 7
        Lab::create([
            'nama_lab' => 'Lab 07',
            'kapasitas' => 36,
            'fasilitas' => 'Windows 11, VS Code, MS Office, Chrome',
        ]);

        // Lab 8 (Desain & 3D Asset)
        Lab::create([
            'nama_lab' => 'Lab 08',
            'kapasitas' => 36,
            'fasilitas' => 'Adobe Photoshop, Blender 3D, Maya, CorelDraw (Design & 3D)',
        ]);

        // Lab 9 (Desain & 3D Asset)
        Lab::create([
            'nama_lab' => 'Lab 09',
            'kapasitas' => 36,
            'fasilitas' => 'Adobe Photoshop, Blender 3D, Maya, CorelDraw (Design & 3D)',
        ]);

        // Lab 10 (Virtual Machine & Linux)
        Lab::create([
            'nama_lab' => 'Lab 10',
            'kapasitas' => 36,
            'fasilitas' => 'VirtualBox, VMware, Ubuntu Linux ISO, PuTTY, Wireshark',
        ]);

        // Lab 11
        Lab::create([
            'nama_lab' => 'Lab 11',
            'kapasitas' => 36,
            'fasilitas' => 'Windows 11, VS Code, MS Office, Chrome',
        ]);
    }
}
