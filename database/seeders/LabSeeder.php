<?php

namespace Database\Seeders;

use App\Models\Lab;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LabSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //Masukkan angka 3 ke dalam daftar dengan detailnya sendiri
        $daftarLab = [
            1  => ['kapasitas' => 30, 'fasilitas' => 'PC Core i5, AC, Proyektor, LAN'],
            2  => ['kapasitas' => 30, 'fasilitas' => 'PC Core i5, AC, Proyektor, LAN'],
            3  => ['kapasitas' => 10, 'fasilitas' => 'Meja Asisten, PC RA, AC'], // Ruang Asisten
            4  => ['kapasitas' => 25, 'fasilitas' => 'Router Cisco, Switch, PC Core i7, AC'],
            5  => ['kapasitas' => 40, 'fasilitas' => 'PC Core i3, AC, Proyektor'],
            6  => ['kapasitas' => 40, 'fasilitas' => 'PC Core i3, AC, Proyektor'],
            7  => ['kapasitas' => 20, 'fasilitas' => 'iMac Retina, AC, Proyektor, LAN'],
            8  => ['kapasitas' => 35, 'fasilitas' => 'PC Core i5, AC, Proyektor'],
            9  => ['kapasitas' => 35, 'fasilitas' => 'PC Core i5, AC, Proyektor'],
            10 => ['kapasitas' => 30, 'fasilitas' => 'PC Core i5, AC, Proyektor, LAN'],
            11 => ['kapasitas' => 30, 'fasilitas' => 'PC Core i5, AC, Proyektor, LAN'],
        ];

        //Looping data
        foreach ($daftarLab as $nomorLab => $detail) {
            
            // Cek kondisi, jika nomorLab adalah 3, namanya diganti khusus
            if ($nomorLab == 3) {
                $namaLab = 'Ruang Asisten';
            } else {
                $namaLab = 'Lab ' . $nomorLab;
            }

            Lab::create([
                'nama_lab'  => $namaLab, 
                'kapasitas' => $detail['kapasitas'],
                'fasilitas' => $detail['fasilitas']
            ]);
        }
    }
}
