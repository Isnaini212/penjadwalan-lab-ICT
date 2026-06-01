<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LabsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('labs')->delete();
        
        \DB::table('labs')->insert(array (
            0 => 
            array (
                'id_lab' => 1,
                'nama_lab' => 'Lab 01',
                'kapasitas' => 32,
                'fasilitas' => 'edasda',
                'created_at' => '2026-05-30 14:42:16',
                'updated_at' => '2026-05-30 14:42:16',
            ),
            1 => 
            array (
                'id_lab' => 2,
                'nama_lab' => 'RUANG RA',
                'kapasitas' => 0,
                'fasilitas' => '-',
                'created_at' => '2026-05-30 15:15:23',
                'updated_at' => '2026-05-30 15:15:23',
            ),
            2 => 
            array (
                'id_lab' => 3,
                'nama_lab' => 'LAB 02',
                'kapasitas' => 40,
                'fasilitas' => '-',
                'created_at' => '2026-05-30 15:51:59',
                'updated_at' => '2026-05-30 15:51:59',
            ),
            3 => 
            array (
                'id_lab' => 4,
                'nama_lab' => 'LAB 07',
                'kapasitas' => 40,
                'fasilitas' => '-',
                'created_at' => '2026-05-30 15:51:59',
                'updated_at' => '2026-05-30 15:51:59',
            ),
            4 => 
            array (
                'id_lab' => 5,
                'nama_lab' => 'LAB 11',
                'kapasitas' => 40,
                'fasilitas' => '-',
                'created_at' => '2026-05-30 15:51:59',
                'updated_at' => '2026-05-30 15:51:59',
            ),
            5 => 
            array (
                'id_lab' => 6,
                'nama_lab' => 'LAB 10',
                'kapasitas' => 40,
                'fasilitas' => '-',
                'created_at' => '2026-05-30 15:51:59',
                'updated_at' => '2026-05-30 15:51:59',
            ),
            6 => 
            array (
                'id_lab' => 7,
                'nama_lab' => 'LAB 08',
                'kapasitas' => 40,
                'fasilitas' => '-',
                'created_at' => '2026-05-30 15:51:59',
                'updated_at' => '2026-05-30 15:51:59',
            ),
            7 => 
            array (
                'id_lab' => 8,
                'nama_lab' => 'LAB 06',
                'kapasitas' => 40,
                'fasilitas' => '-',
                'created_at' => '2026-05-30 15:51:59',
                'updated_at' => '2026-05-30 15:51:59',
            ),
            8 => 
            array (
                'id_lab' => 9,
                'nama_lab' => 'LAB 04',
                'kapasitas' => 40,
                'fasilitas' => '-',
                'created_at' => '2026-05-30 15:51:59',
                'updated_at' => '2026-05-30 15:51:59',
            ),
            9 => 
            array (
                'id_lab' => 10,
                'nama_lab' => 'LAB 05',
                'kapasitas' => 40,
                'fasilitas' => '-',
                'created_at' => '2026-05-30 15:51:59',
                'updated_at' => '2026-05-30 15:51:59',
            ),
            10 => 
            array (
                'id_lab' => 11,
                'nama_lab' => 'LAB TBD',
                'kapasitas' => 40,
                'fasilitas' => '-',
                'created_at' => '2026-05-30 15:51:59',
                'updated_at' => '2026-05-30 15:51:59',
            ),
            11 => 
            array (
                'id_lab' => 12,
                'nama_lab' => 'LAB 09',
                'kapasitas' => 40,
                'fasilitas' => '-',
                'created_at' => '2026-05-30 15:51:59',
                'updated_at' => '2026-05-30 15:51:59',
            ),
        ));
        
        
    }
}