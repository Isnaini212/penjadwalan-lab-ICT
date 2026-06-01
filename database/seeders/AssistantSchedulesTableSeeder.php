<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class AssistantSchedulesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('assistant_schedules')->delete();
        
        \DB::table('assistant_schedules')->insert(array (
            0 => 
            array (
                'id_asisten' => 389,
                'nama_asisten' => 'Rizky Saputra',
                'hari' => 'Rabu',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '10:35:00',
            'mata_kuliah' => 'REKWEB (AA)',
                'created_at' => '2026-05-30 14:36:56',
                'updated_at' => '2026-05-30 14:36:56',
            ),
            1 => 
            array (
                'id_asisten' => 390,
                'nama_asisten' => 'Rizky Saputra',
                'hari' => 'Kamis',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '10:35:00',
            'mata_kuliah' => 'KRIMFOR (AB)',
                'created_at' => '2026-05-30 14:36:56',
                'updated_at' => '2026-05-30 14:36:56',
            ),
            2 => 
            array (
                'id_asisten' => 391,
                'nama_asisten' => 'Rizky Saputra',
                'hari' => 'Selasa',
                'jam_mulai' => '08:55:00',
                'jam_selesai' => '11:30:00',
            'mata_kuliah' => 'PL/SQL (AA)',
                'created_at' => '2026-05-30 14:36:56',
                'updated_at' => '2026-05-30 14:36:56',
            ),
            3 => 
            array (
                'id_asisten' => 392,
                'nama_asisten' => 'Rizky Saputra',
                'hari' => 'Senin',
                'jam_mulai' => '10:40:00',
                'jam_selesai' => '13:20:00',
            'mata_kuliah' => 'IPBO (AA)',
                'created_at' => '2026-05-30 14:36:56',
                'updated_at' => '2026-05-30 14:36:56',
            ),
            4 => 
            array (
                'id_asisten' => 393,
                'nama_asisten' => 'Rizky Saputra',
                'hari' => 'Rabu',
                'jam_mulai' => '10:40:00',
                'jam_selesai' => '13:20:00',
            'mata_kuliah' => 'BPP (AA)',
                'created_at' => '2026-05-30 14:36:56',
                'updated_at' => '2026-05-30 14:36:56',
            ),
            5 => 
            array (
                'id_asisten' => 394,
                'nama_asisten' => 'Rizky Saputra',
                'hari' => 'Kamis',
                'jam_mulai' => '10:40:00',
                'jam_selesai' => '13:20:00',
            'mata_kuliah' => 'Desain Grafis (AB)',
                'created_at' => '2026-05-30 14:36:56',
                'updated_at' => '2026-05-30 14:36:56',
            ),
            6 => 
            array (
                'id_asisten' => 395,
                'nama_asisten' => 'Rizky Saputra',
                'hari' => 'Selasa',
                'jam_mulai' => '13:25:00',
                'jam_selesai' => '16:05:00',
            'mata_kuliah' => 'Analisis Sentimen (AA)',
                'created_at' => '2026-05-30 14:36:56',
                'updated_at' => '2026-05-30 14:36:56',
            ),
            7 => 
            array (
                'id_asisten' => 396,
                'nama_asisten' => 'Rizky Saputra',
                'hari' => 'Jumat',
                'jam_mulai' => '13:25:00',
                'jam_selesai' => '16:05:00',
            'mata_kuliah' => 'Media Sosial Dan Periklanan (AA)',
                'created_at' => '2026-05-30 14:36:56',
                'updated_at' => '2026-05-30 14:36:56',
            ),
            8 => 
            array (
                'id_asisten' => 397,
                'nama_asisten' => 'Muhammad Rifqi Fauzan',
                'hari' => 'Senin',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '10:35:00',
                'mata_kuliah' => 'PL/SQL',
                'created_at' => '2026-05-30 14:36:56',
                'updated_at' => '2026-05-30 14:36:56',
            ),
            9 => 
            array (
                'id_asisten' => 398,
                'nama_asisten' => 'Muhammad Rifqi Fauzan',
                'hari' => 'Rabu',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '10:35:00',
                'mata_kuliah' => 'RekWeb',
                'created_at' => '2026-05-30 14:36:56',
                'updated_at' => '2026-05-30 14:36:56',
            ),
            10 => 
            array (
                'id_asisten' => 399,
                'nama_asisten' => 'Muhammad Rifqi Fauzan',
                'hari' => 'Kamis',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '10:35:00',
                'mata_kuliah' => 'P.Ekonomi, Man., Bisn.',
                'created_at' => '2026-05-30 14:36:56',
                'updated_at' => '2026-05-30 14:36:56',
            ),
            11 => 
            array (
                'id_asisten' => 400,
                'nama_asisten' => 'Muhammad Rifqi Fauzan',
                'hari' => 'Senin',
                'jam_mulai' => '10:40:00',
                'jam_selesai' => '13:20:00',
                'mata_kuliah' => 'Audit SI',
                'created_at' => '2026-05-30 14:36:56',
                'updated_at' => '2026-05-30 14:36:56',
            ),
            12 => 
            array (
                'id_asisten' => 401,
                'nama_asisten' => 'Muhammad Rifqi Fauzan',
                'hari' => 'Rabu',
                'jam_mulai' => '10:40:00',
                'jam_selesai' => '13:20:00',
                'mata_kuliah' => 'Computer Vision',
                'created_at' => '2026-05-30 14:36:56',
                'updated_at' => '2026-05-30 14:36:56',
            ),
            13 => 
            array (
                'id_asisten' => 402,
                'nama_asisten' => 'Muhammad Rifqi Fauzan',
                'hari' => 'Kamis',
                'jam_mulai' => '10:40:00',
                'jam_selesai' => '13:20:00',
                'mata_kuliah' => 'Des. Grafis',
                'created_at' => '2026-05-30 14:36:56',
                'updated_at' => '2026-05-30 14:36:56',
            ),
            14 => 
            array (
                'id_asisten' => 403,
                'nama_asisten' => 'Muhammad Rifqi Fauzan',
                'hari' => 'Selasa',
                'jam_mulai' => '13:25:00',
                'jam_selesai' => '16:05:00',
                'mata_kuliah' => 'P. Web 1',
                'created_at' => '2026-05-30 14:36:56',
                'updated_at' => '2026-05-30 14:36:56',
            ),
            15 => 
            array (
                'id_asisten' => 404,
                'nama_asisten' => 'Muhammad Rifqi Fauzan',
                'hari' => 'Rabu',
                'jam_mulai' => '13:25:00',
                'jam_selesai' => '16:05:00',
                'mata_kuliah' => 'Analisis Sentimen',
                'created_at' => '2026-05-30 14:36:56',
                'updated_at' => '2026-05-30 14:36:56',
            ),
            16 => 
            array (
                'id_asisten' => 405,
                'nama_asisten' => 'HIdayat Ramadhani Supriyatna',
                'hari' => 'Jumat',
                'jam_mulai' => '08:00:00',
                'jam_selesai' => '09:40:00',
                'mata_kuliah' => 'Etika Profesi
(AB)',
                    'created_at' => '2026-05-30 14:36:56',
                    'updated_at' => '2026-05-30 14:36:56',
                ),
                17 => 
                array (
                    'id_asisten' => 406,
                    'nama_asisten' => 'HIdayat Ramadhani Supriyatna',
                    'hari' => 'Senin',
                    'jam_mulai' => '08:00:00',
                    'jam_selesai' => '10:35:00',
                'mata_kuliah' => 'Komputasi Awan (AA)',
                    'created_at' => '2026-05-30 14:36:56',
                    'updated_at' => '2026-05-30 14:36:56',
                ),
                18 => 
                array (
                    'id_asisten' => 407,
                    'nama_asisten' => 'HIdayat Ramadhani Supriyatna',
                    'hari' => 'Rabu',
                    'jam_mulai' => '08:00:00',
                    'jam_selesai' => '10:35:00',
                'mata_kuliah' => 'Rekayasa Web (AA)',
                    'created_at' => '2026-05-30 14:36:56',
                    'updated_at' => '2026-05-30 14:36:56',
                ),
                19 => 
                array (
                    'id_asisten' => 408,
                    'nama_asisten' => 'HIdayat Ramadhani Supriyatna',
                    'hari' => 'Selasa',
                    'jam_mulai' => '10:40:00',
                    'jam_selesai' => '12:25:00',
                    'mata_kuliah' => 'Java Web 
Programming (AB)',
                        'created_at' => '2026-05-30 14:36:56',
                        'updated_at' => '2026-05-30 14:36:56',
                    ),
                    20 => 
                    array (
                        'id_asisten' => 409,
                        'nama_asisten' => 'HIdayat Ramadhani Supriyatna',
                        'hari' => 'Rabu',
                        'jam_mulai' => '10:40:00',
                        'jam_selesai' => '13:20:00',
                        'mata_kuliah' => 'Bahasa Pemrograman
Python (AA)',
                            'created_at' => '2026-05-30 14:36:56',
                            'updated_at' => '2026-05-30 14:36:56',
                        ),
                        21 => 
                        array (
                            'id_asisten' => 410,
                            'nama_asisten' => 'HIdayat Ramadhani Supriyatna',
                            'hari' => 'Kamis',
                            'jam_mulai' => '11:35:00',
                            'jam_selesai' => '13:20:00',
                            'mata_kuliah' => 'Komas 
(AA)',
                                'created_at' => '2026-05-30 14:36:56',
                                'updated_at' => '2026-05-30 14:36:56',
                            ),
                            22 => 
                            array (
                                'id_asisten' => 411,
                                'nama_asisten' => 'HIdayat Ramadhani Supriyatna',
                                'hari' => 'Senin',
                                'jam_mulai' => '13:25:00',
                                'jam_selesai' => '16:05:00',
                            'mata_kuliah' => 'JARKOM 2 (AA)',
                                'created_at' => '2026-05-30 14:36:56',
                                'updated_at' => '2026-05-30 14:36:56',
                            ),
                            23 => 
                            array (
                                'id_asisten' => 412,
                                'nama_asisten' => 'HIdayat Ramadhani Supriyatna',
                                'hari' => 'Selasa',
                                'jam_mulai' => '13:25:00',
                                'jam_selesai' => '16:05:00',
                                'mata_kuliah' => 'Analisis Sentimen
(AA)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                24 => 
                                array (
                                    'id_asisten' => 413,
                                    'nama_asisten' => 'HIdayat Ramadhani Supriyatna',
                                    'hari' => 'Rabu',
                                    'jam_mulai' => '16:10:00',
                                    'jam_selesai' => '18:50:00',
                                'mata_kuliah' => 'Keamanan Jaringan (AA)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                25 => 
                                array (
                                    'id_asisten' => 414,
                                    'nama_asisten' => 'Rafif Ali Rachman',
                                    'hari' => 'Senin',
                                    'jam_mulai' => '08:00:00',
                                    'jam_selesai' => '09:40:00',
                                'mata_kuliah' => 'Komunikasi Publik dan Presentasi (AE)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                26 => 
                                array (
                                    'id_asisten' => 415,
                                    'nama_asisten' => 'Rafif Ali Rachman',
                                    'hari' => 'Jumat',
                                    'jam_mulai' => '08:00:00',
                                    'jam_selesai' => '09:40:00',
                                'mata_kuliah' => 'Etika Profesi (AB)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                27 => 
                                array (
                                    'id_asisten' => 416,
                                    'nama_asisten' => 'Rafif Ali Rachman',
                                    'hari' => 'Kamis',
                                    'jam_mulai' => '08:00:00',
                                    'jam_selesai' => '10:35:00',
                                'mata_kuliah' => 'PEMB (AA)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                28 => 
                                array (
                                    'id_asisten' => 417,
                                    'nama_asisten' => 'Rafif Ali Rachman',
                                    'hari' => 'Senin',
                                    'jam_mulai' => '10:40:00',
                                    'jam_selesai' => '13:20:00',
                                'mata_kuliah' => 'Audit SI (AA)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                29 => 
                                array (
                                    'id_asisten' => 418,
                                    'nama_asisten' => 'Rafif Ali Rachman',
                                    'hari' => 'Selasa',
                                    'jam_mulai' => '10:40:00',
                                    'jam_selesai' => '13:20:00',
                                'mata_kuliah' => 'Analisa Big Data (AA)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                30 => 
                                array (
                                    'id_asisten' => 419,
                                    'nama_asisten' => 'Rafif Ali Rachman',
                                    'hari' => 'Kamis',
                                    'jam_mulai' => '11:35:00',
                                    'jam_selesai' => '13:20:00',
                                'mata_kuliah' => 'Komputer Masyarakat (AA)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                31 => 
                                array (
                                    'id_asisten' => 420,
                                    'nama_asisten' => 'Rafif Ali Rachman',
                                    'hari' => 'Selasa',
                                    'jam_mulai' => '13:25:00',
                                    'jam_selesai' => '16:05:00',
                                'mata_kuliah' => 'Analisis Sentimen (AA)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                32 => 
                                array (
                                    'id_asisten' => 421,
                                    'nama_asisten' => 'Rafif Ali Rachman',
                                    'hari' => 'Kamis',
                                    'jam_mulai' => '13:25:00',
                                    'jam_selesai' => '16:05:00',
                                'mata_kuliah' => 'Teori Kriminologi Modern (AA)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                33 => 
                                array (
                                    'id_asisten' => 422,
                                    'nama_asisten' => 'Rafif Ali Rachman',
                                    'hari' => 'Jumat',
                                    'jam_mulai' => '13:25:00',
                                    'jam_selesai' => '16:05:00',
                                'mata_kuliah' => 'Medsos dan Periklanan (AA)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                34 => 
                                array (
                                    'id_asisten' => 423,
                                    'nama_asisten' => 'NAUFA AULIA SABILA AZZAHRA',
                                    'hari' => 'Senin',
                                    'jam_mulai' => '08:00:00',
                                    'jam_selesai' => '09:40:00',
                                'mata_kuliah' => 'Metris (ae)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                35 => 
                                array (
                                    'id_asisten' => 424,
                                    'nama_asisten' => 'NAUFA AULIA SABILA AZZAHRA',
                                    'hari' => 'Rabu',
                                    'jam_mulai' => '08:00:00',
                                    'jam_selesai' => '10:35:00',
                                'mata_kuliah' => 'PEMWEB 1 (AD)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                36 => 
                                array (
                                    'id_asisten' => 425,
                                    'nama_asisten' => 'NAUFA AULIA SABILA AZZAHRA',
                                    'hari' => 'Kamis',
                                    'jam_mulai' => '08:00:00',
                                    'jam_selesai' => '10:35:00',
                                'mata_kuliah' => 'Administrasi LiNUX (AB)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                37 => 
                                array (
                                    'id_asisten' => 426,
                                    'nama_asisten' => 'NAUFA AULIA SABILA AZZAHRA',
                                    'hari' => 'Jumat',
                                    'jam_mulai' => '08:00:00',
                                    'jam_selesai' => '10:35:00',
                                'mata_kuliah' => 'KWJ (AB)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                38 => 
                                array (
                                    'id_asisten' => 427,
                                    'nama_asisten' => 'NAUFA AULIA SABILA AZZAHRA',
                                    'hari' => 'Rabu',
                                    'jam_mulai' => '10:40:00',
                                    'jam_selesai' => '13:20:00',
                                'mata_kuliah' => 'Keamanan Siber (Ac)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                39 => 
                                array (
                                    'id_asisten' => 428,
                                    'nama_asisten' => 'NAUFA AULIA SABILA AZZAHRA',
                                    'hari' => 'Senin',
                                    'jam_mulai' => '13:25:00',
                                    'jam_selesai' => '16:05:00',
                                'mata_kuliah' => 'Moprog (AF)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                40 => 
                                array (
                                    'id_asisten' => 429,
                                    'nama_asisten' => 'NAUFA AULIA SABILA AZZAHRA',
                                    'hari' => 'Selasa',
                                    'jam_mulai' => '13:25:00',
                                    'jam_selesai' => '16:05:00',
                                'mata_kuliah' => 'Jarkom (AB)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                41 => 
                                array (
                                    'id_asisten' => 430,
                                    'nama_asisten' => 'NAUFA AULIA SABILA AZZAHRA',
                                    'hari' => 'Kamis',
                                    'jam_mulai' => '13:25:00',
                                    'jam_selesai' => '16:05:00',
                                'mata_kuliah' => 'MPPL (AE)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                42 => 
                                array (
                                    'id_asisten' => 431,
                                    'nama_asisten' => 'MUHAMMAD RAZIN HAIDAR KARIM',
                                    'hari' => 'Rabu',
                                    'jam_mulai' => '08:00:00',
                                    'jam_selesai' => '09:40:00',
                                'mata_kuliah' => 'Metris (AC)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                43 => 
                                array (
                                    'id_asisten' => 432,
                                    'nama_asisten' => 'MUHAMMAD RAZIN HAIDAR KARIM',
                                    'hari' => 'Selasa',
                                    'jam_mulai' => '08:00:00',
                                    'jam_selesai' => '10:35:00',
                                'mata_kuliah' => 'Keamanan Siber (AB)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                44 => 
                                array (
                                    'id_asisten' => 433,
                                    'nama_asisten' => 'MUHAMMAD RAZIN HAIDAR KARIM',
                                    'hari' => 'Kamis',
                                    'jam_mulai' => '08:00:00',
                                    'jam_selesai' => '10:35:00',
                                'mata_kuliah' => 'Administrasi Linux (AB)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                45 => 
                                array (
                                    'id_asisten' => 434,
                                    'nama_asisten' => 'MUHAMMAD RAZIN HAIDAR KARIM',
                                    'hari' => 'Jumat',
                                    'jam_mulai' => '08:00:00',
                                    'jam_selesai' => '10:35:00',
                                'mata_kuliah' => 'Jarkom (AE)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                46 => 
                                array (
                                    'id_asisten' => 435,
                                    'nama_asisten' => 'MUHAMMAD RAZIN HAIDAR KARIM',
                                    'hari' => 'Senin',
                                    'jam_mulai' => '10:40:00',
                                    'jam_selesai' => '13:20:00',
                                'mata_kuliah' => 'Moprog (AC)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                47 => 
                                array (
                                    'id_asisten' => 436,
                                    'nama_asisten' => 'MUHAMMAD RAZIN HAIDAR KARIM',
                                    'hari' => 'Kamis',
                                    'jam_mulai' => '10:40:00',
                                    'jam_selesai' => '13:20:00',
                                'mata_kuliah' => 'PEMWEB 1 (AA)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                48 => 
                                array (
                                    'id_asisten' => 437,
                                    'nama_asisten' => 'MUHAMMAD RAZIN HAIDAR KARIM',
                                    'hari' => 'Senin',
                                    'jam_mulai' => '13:25:00',
                                    'jam_selesai' => '16:05:00',
                                'mata_kuliah' => 'KWJ (AA)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                49 => 
                                array (
                                    'id_asisten' => 438,
                                    'nama_asisten' => 'MUHAMMAD RAZIN HAIDAR KARIM',
                                    'hari' => 'Rabu',
                                    'jam_mulai' => '13:25:00',
                                    'jam_selesai' => '16:05:00',
                                'mata_kuliah' => 'MPPL (AA)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                50 => 
                                array (
                                    'id_asisten' => 439,
                                    'nama_asisten' => 'AHVAZ HAIDAR GAZA WAHYUDI',
                                    'hari' => 'Senin',
                                    'jam_mulai' => '08:00:00',
                                    'jam_selesai' => '09:40:00',
                                'mata_kuliah' => 'METRIS (AE)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                51 => 
                                array (
                                    'id_asisten' => 440,
                                    'nama_asisten' => 'AHVAZ HAIDAR GAZA WAHYUDI',
                                    'hari' => 'Rabu',
                                    'jam_mulai' => '08:00:00',
                                    'jam_selesai' => '10:35:00',
                                'mata_kuliah' => 'MPPL (AH)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                52 => 
                                array (
                                    'id_asisten' => 441,
                                    'nama_asisten' => 'AHVAZ HAIDAR GAZA WAHYUDI',
                                    'hari' => 'Kamis',
                                    'jam_mulai' => '08:00:00',
                                    'jam_selesai' => '10:35:00',
                                'mata_kuliah' => 'Administrasi Linux (AB)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                53 => 
                                array (
                                    'id_asisten' => 442,
                                    'nama_asisten' => 'AHVAZ HAIDAR GAZA WAHYUDI',
                                    'hari' => 'Jumat',
                                    'jam_mulai' => '08:00:00',
                                    'jam_selesai' => '10:35:00',
                                'mata_kuliah' => 'Jarkom (AE)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                54 => 
                                array (
                                    'id_asisten' => 443,
                                    'nama_asisten' => 'AHVAZ HAIDAR GAZA WAHYUDI',
                                    'hari' => 'Senin',
                                    'jam_mulai' => '10:40:00',
                                    'jam_selesai' => '12:26:00',
                                'mata_kuliah' => 'MOPROG (AC)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                55 => 
                                array (
                                    'id_asisten' => 444,
                                    'nama_asisten' => 'AHVAZ HAIDAR GAZA WAHYUDI',
                                    'hari' => 'Rabu',
                                    'jam_mulai' => '10:40:00',
                                    'jam_selesai' => '12:26:00',
                                'mata_kuliah' => 'Keamanan Siber (Ac)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                56 => 
                                array (
                                    'id_asisten' => 445,
                                    'nama_asisten' => 'AHVAZ HAIDAR GAZA WAHYUDI',
                                    'hari' => 'Kamis',
                                    'jam_mulai' => '10:40:00',
                                    'jam_selesai' => '12:26:00',
                                'mata_kuliah' => 'PEMWEB 1 (AA)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                57 => 
                                array (
                                    'id_asisten' => 446,
                                    'nama_asisten' => 'AHVAZ HAIDAR GAZA WAHYUDI',
                                    'hari' => 'Kamis',
                                    'jam_mulai' => '13:25:00',
                                    'jam_selesai' => '16:05:00',
                                'mata_kuliah' => 'KWJ (AA)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                58 => 
                                array (
                                    'id_asisten' => 447,
                                    'nama_asisten' => 'Fredy Dwi Saputra',
                                    'hari' => 'Rabu',
                                    'jam_mulai' => '08:00:00',
                                    'jam_selesai' => '10:35:00',
                                'mata_kuliah' => 'Rekayasa Web (AA)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                59 => 
                                array (
                                    'id_asisten' => 448,
                                    'nama_asisten' => 'Fredy Dwi Saputra',
                                    'hari' => 'Kamis',
                                    'jam_mulai' => '08:00:00',
                                    'jam_selesai' => '10:35:00',
                                'mata_kuliah' => 'Visualisasi Data (AA)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                60 => 
                                array (
                                    'id_asisten' => 449,
                                    'nama_asisten' => 'Fredy Dwi Saputra',
                                    'hari' => 'Jumat',
                                    'jam_mulai' => '08:00:00',
                                    'jam_selesai' => '10:35:00',
                                'mata_kuliah' => 'Keamanan Web dan Jaringan (AB)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                61 => 
                                array (
                                    'id_asisten' => 450,
                                    'nama_asisten' => 'Fredy Dwi Saputra',
                                    'hari' => 'Senin',
                                    'jam_mulai' => '10:40:00',
                                    'jam_selesai' => '13:20:00',
                                'mata_kuliah' => 'IPBO (AA)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                62 => 
                                array (
                                    'id_asisten' => 451,
                                    'nama_asisten' => 'Fredy Dwi Saputra',
                                    'hari' => 'Rabu',
                                    'jam_mulai' => '10:40:00',
                                    'jam_selesai' => '13:20:00',
                                'mata_kuliah' => 'Java Web Programming (AA)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                63 => 
                                array (
                                    'id_asisten' => 452,
                                    'nama_asisten' => 'Fredy Dwi Saputra',
                                    'hari' => 'Kamis',
                                    'jam_mulai' => '10:40:00',
                                    'jam_selesai' => '13:20:00',
                                'mata_kuliah' => 'Keamanan Komputer (AB)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                64 => 
                                array (
                                    'id_asisten' => 453,
                                    'nama_asisten' => 'Fredy Dwi Saputra',
                                    'hari' => 'Selasa',
                                    'jam_mulai' => '13:25:00',
                                    'jam_selesai' => '16:05:00',
                                'mata_kuliah' => 'Analisis Sentimen (AA)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                65 => 
                                array (
                                    'id_asisten' => 454,
                                    'nama_asisten' => 'Fredy Dwi Saputra',
                                    'hari' => 'Jumat',
                                    'jam_mulai' => '13:25:00',
                                    'jam_selesai' => '16:05:00',
                                'mata_kuliah' => 'Python Tingkat Lanjut (AF)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                66 => 
                                array (
                                    'id_asisten' => 455,
                                    'nama_asisten' => 'Bima Rasta Guevara',
                                    'hari' => 'Senin',
                                    'jam_mulai' => '08:00:00',
                                    'jam_selesai' => '10:35:00',
                                    'mata_kuliah' => 'PL/SQL',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                67 => 
                                array (
                                    'id_asisten' => 456,
                                    'nama_asisten' => 'Bima Rasta Guevara',
                                    'hari' => 'Rabu',
                                    'jam_mulai' => '08:00:00',
                                    'jam_selesai' => '10:35:00',
                                    'mata_kuliah' => 'RekWeb',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                68 => 
                                array (
                                    'id_asisten' => 457,
                                    'nama_asisten' => 'Bima Rasta Guevara',
                                    'hari' => 'Rabu',
                                    'jam_mulai' => '10:40:00',
                                    'jam_selesai' => '13:20:00',
                                    'mata_kuliah' => 'Java Web 
Programming',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                69 => 
                                array (
                                    'id_asisten' => 458,
                                    'nama_asisten' => 'Bima Rasta Guevara',
                                    'hari' => 'Kamis',
                                    'jam_mulai' => '10:40:00',
                                    'jam_selesai' => '13:20:00',
                                    'mata_kuliah' => 'Des. Grafis',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                70 => 
                                array (
                                    'id_asisten' => 459,
                                    'nama_asisten' => 'Bima Rasta Guevara',
                                    'hari' => 'Senin',
                                    'jam_mulai' => '13:25:00',
                                    'jam_selesai' => '16:05:00',
                                'mata_kuliah' => 'JARKOM 2 (AA)',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                71 => 
                                array (
                                    'id_asisten' => 460,
                                    'nama_asisten' => 'Bima Rasta Guevara',
                                    'hari' => 'Selasa',
                                    'jam_mulai' => '13:25:00',
                                    'jam_selesai' => '16:05:00',
                                    'mata_kuliah' => 'P. Web 1',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                72 => 
                                array (
                                    'id_asisten' => 461,
                                    'nama_asisten' => 'Bima Rasta Guevara',
                                    'hari' => 'Rabu',
                                    'jam_mulai' => '13:25:00',
                                    'jam_selesai' => '16:05:00',
                                    'mata_kuliah' => 'Analisis Sentimen',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                73 => 
                                array (
                                    'id_asisten' => 462,
                                    'nama_asisten' => 'Bima Rasta Guevara',
                                    'hari' => 'Rabu',
                                    'jam_mulai' => '16:10:00',
                                    'jam_selesai' => '18:50:00',
                                    'mata_kuliah' => 'Keamanan Jaringan',
                                    'created_at' => '2026-05-30 14:36:56',
                                    'updated_at' => '2026-05-30 14:36:56',
                                ),
                                74 => 
                                array (
                                    'id_asisten' => 463,
                                    'nama_asisten' => 'Fauzi Alfadhillah',
                                    'hari' => 'Jumat',
                                    'jam_mulai' => '08:00:00',
                                    'jam_selesai' => '09:40:00',
                                    'mata_kuliah' => 'Etika Profesi
(AB)',
                                        'created_at' => '2026-05-30 14:36:56',
                                        'updated_at' => '2026-05-30 14:36:56',
                                    ),
                                    75 => 
                                    array (
                                        'id_asisten' => 464,
                                        'nama_asisten' => 'Fauzi Alfadhillah',
                                        'hari' => 'Senin',
                                        'jam_mulai' => '08:00:00',
                                        'jam_selesai' => '10:35:00',
                                    'mata_kuliah' => 'PL/SQL (AA)',
                                        'created_at' => '2026-05-30 14:36:56',
                                        'updated_at' => '2026-05-30 14:36:56',
                                    ),
                                    76 => 
                                    array (
                                        'id_asisten' => 465,
                                        'nama_asisten' => 'Fauzi Alfadhillah',
                                        'hari' => 'Rabu',
                                        'jam_mulai' => '08:00:00',
                                        'jam_selesai' => '10:35:00',
                                    'mata_kuliah' => 'SPK (AA)',
                                        'created_at' => '2026-05-30 14:36:56',
                                        'updated_at' => '2026-05-30 14:36:56',
                                    ),
                                    77 => 
                                    array (
                                        'id_asisten' => 466,
                                        'nama_asisten' => 'Fauzi Alfadhillah',
                                        'hari' => 'Kamis',
                                        'jam_mulai' => '08:00:00',
                                        'jam_selesai' => '10:35:00',
                                    'mata_kuliah' => 'Keamanan Siber (AD)',
                                        'created_at' => '2026-05-30 14:36:56',
                                        'updated_at' => '2026-05-30 14:36:56',
                                    ),
                                    78 => 
                                    array (
                                        'id_asisten' => 467,
                                        'nama_asisten' => 'Fauzi Alfadhillah',
                                        'hari' => 'Selasa',
                                        'jam_mulai' => '11:35:00',
                                        'jam_selesai' => '12:25:00',
                                        'mata_kuliah' => '11.35-12.25',
                                        'created_at' => '2026-05-30 14:36:56',
                                        'updated_at' => '2026-05-30 14:36:56',
                                    ),
                                    79 => 
                                    array (
                                        'id_asisten' => 468,
                                        'nama_asisten' => 'Fauzi Alfadhillah',
                                        'hari' => 'Senin',
                                        'jam_mulai' => '10:40:00',
                                        'jam_selesai' => '13:20:00',
                                    'mata_kuliah' => 'Audit SI (AA)',
                                        'created_at' => '2026-05-30 14:36:56',
                                        'updated_at' => '2026-05-30 14:36:56',
                                    ),
                                    80 => 
                                    array (
                                        'id_asisten' => 469,
                                        'nama_asisten' => 'Fauzi Alfadhillah',
                                        'hari' => 'Selasa',
                                        'jam_mulai' => '12:30:00',
                                        'jam_selesai' => '13:20:00',
                                        'mata_kuliah' => '12.30-13.20',
                                        'created_at' => '2026-05-30 14:36:56',
                                        'updated_at' => '2026-05-30 14:36:56',
                                    ),
                                    81 => 
                                    array (
                                        'id_asisten' => 470,
                                        'nama_asisten' => 'Fauzi Alfadhillah',
                                        'hari' => 'Kamis',
                                        'jam_mulai' => '11:35:00',
                                        'jam_selesai' => '13:20:00',
                                    'mata_kuliah' => 'Komputer Masyarakat (AB)',
                                        'created_at' => '2026-05-30 14:36:56',
                                        'updated_at' => '2026-05-30 14:36:56',
                                    ),
                                    82 => 
                                    array (
                                        'id_asisten' => 471,
                                        'nama_asisten' => 'Fauzi Alfadhillah',
                                        'hari' => 'Jumat',
                                        'jam_mulai' => '13:25:00',
                                        'jam_selesai' => '15:10:00',
                                    'mata_kuliah' => 'Interpersonal Skill (AE)',
                                        'created_at' => '2026-05-30 14:36:56',
                                        'updated_at' => '2026-05-30 14:36:56',
                                    ),
                                    83 => 
                                    array (
                                        'id_asisten' => 472,
                                        'nama_asisten' => 'Fauzi Alfadhillah',
                                        'hari' => 'Senin',
                                        'jam_mulai' => '13:25:00',
                                        'jam_selesai' => '16:05:00',
                                    'mata_kuliah' => 'JARKOM 2 (AA)',
                                        'created_at' => '2026-05-30 14:36:56',
                                        'updated_at' => '2026-05-30 14:36:56',
                                    ),
                                    84 => 
                                    array (
                                        'id_asisten' => 473,
                                        'nama_asisten' => 'Fauzi Alfadhillah',
                                        'hari' => 'Rabu',
                                        'jam_mulai' => '13:25:00',
                                        'jam_selesai' => '16:05:00',
                                    'mata_kuliah' => 'Analisis Sentimen (AB)',
                                        'created_at' => '2026-05-30 14:36:56',
                                        'updated_at' => '2026-05-30 14:36:56',
                                    ),
                                    85 => 
                                    array (
                                        'id_asisten' => 474,
                                        'nama_asisten' => 'Farand Effraim',
                                        'hari' => 'Jumat',
                                        'jam_mulai' => '08:00:00',
                                        'jam_selesai' => '08:50:00',
                                    'mata_kuliah' => 'AWBL (AG)',
                                        'created_at' => '2026-05-30 14:36:56',
                                        'updated_at' => '2026-05-30 14:36:56',
                                    ),
                                    86 => 
                                    array (
                                        'id_asisten' => 475,
                                        'nama_asisten' => 'Farand Effraim',
                                        'hari' => 'Senin',
                                        'jam_mulai' => '08:00:00',
                                        'jam_selesai' => '10:35:00',
                                    'mata_kuliah' => 'PL SQL(AA)',
                                        'created_at' => '2026-05-30 14:36:56',
                                        'updated_at' => '2026-05-30 14:36:56',
                                    ),
                                    87 => 
                                    array (
                                        'id_asisten' => 476,
                                        'nama_asisten' => 'Farand Effraim',
                                        'hari' => 'Rabu',
                                        'jam_mulai' => '08:00:00',
                                        'jam_selesai' => '10:35:00',
                                    'mata_kuliah' => 'PWEB 1 (AD)',
                                        'created_at' => '2026-05-30 14:36:56',
                                        'updated_at' => '2026-05-30 14:36:56',
                                    ),
                                    88 => 
                                    array (
                                        'id_asisten' => 477,
                                        'nama_asisten' => 'Farand Effraim',
                                        'hari' => 'Kamis',
                                        'jam_mulai' => '08:00:00',
                                        'jam_selesai' => '10:35:00',
                                    'mata_kuliah' => 'CMS (AA)',
                                        'created_at' => '2026-05-30 14:36:56',
                                        'updated_at' => '2026-05-30 14:36:56',
                                    ),
                                    89 => 
                                    array (
                                        'id_asisten' => 478,
                                        'nama_asisten' => 'Farand Effraim',
                                        'hari' => 'Senin',
                                        'jam_mulai' => '10:40:00',
                                        'jam_selesai' => '13:20:00',
                                    'mata_kuliah' => 'AUDIT SISTEM INFORMASI (AA)',
                                        'created_at' => '2026-05-30 14:36:56',
                                        'updated_at' => '2026-05-30 14:36:56',
                                    ),
                                    90 => 
                                    array (
                                        'id_asisten' => 479,
                                        'nama_asisten' => 'Farand Effraim',
                                        'hari' => 'Senin',
                                        'jam_mulai' => '13:25:00',
                                        'jam_selesai' => '16:05:00',
                                    'mata_kuliah' => 'KECERDASAN BISNIS (AA)',
                                        'created_at' => '2026-05-30 14:36:56',
                                        'updated_at' => '2026-05-30 14:36:56',
                                    ),
                                    91 => 
                                    array (
                                        'id_asisten' => 480,
                                        'nama_asisten' => 'Farand Effraim',
                                        'hari' => 'Selasa',
                                        'jam_mulai' => '13:25:00',
                                        'jam_selesai' => '16:05:00',
                                    'mata_kuliah' => 'KOMPUTER FORENSIK (AA)',
                                        'created_at' => '2026-05-30 14:36:56',
                                        'updated_at' => '2026-05-30 14:36:56',
                                    ),
                                    92 => 
                                    array (
                                        'id_asisten' => 481,
                                        'nama_asisten' => 'Farand Effraim',
                                        'hari' => 'Rabu',
                                        'jam_mulai' => '13:25:00',
                                        'jam_selesai' => '16:05:00',
                                    'mata_kuliah' => 'E COMMERCE (AA)',
                                        'created_at' => '2026-05-30 14:36:56',
                                        'updated_at' => '2026-05-30 14:36:56',
                                    ),
                                    93 => 
                                    array (
                                        'id_asisten' => 482,
                                        'nama_asisten' => 'Farand Effraim',
                                        'hari' => 'Jumat',
                                        'jam_mulai' => '13:25:00',
                                        'jam_selesai' => '16:05:00',
                                    'mata_kuliah' => 'MEDSOS (AA)',
                                        'created_at' => '2026-05-30 14:36:56',
                                        'updated_at' => '2026-05-30 14:36:56',
                                    ),
                                    94 => 
                                    array (
                                        'id_asisten' => 483,
                                        'nama_asisten' => 'INAYA ZEHAN KALYZTA',
                                        'hari' => 'Senin',
                                        'jam_mulai' => '08:00:00',
                                        'jam_selesai' => '09:40:00',
                                    'mata_kuliah' => 'METRIS (AE)',
                                        'created_at' => '2026-05-30 14:36:56',
                                        'updated_at' => '2026-05-30 14:36:56',
                                    ),
                                    95 => 
                                    array (
                                        'id_asisten' => 484,
                                        'nama_asisten' => 'INAYA ZEHAN KALYZTA',
                                        'hari' => 'Rabu',
                                        'jam_mulai' => '08:00:00',
                                        'jam_selesai' => '10:35:00',
                                        'mata_kuliah' => 'Manajemen Proyek 
Perangkat Lunak
(AH)',
                                            'created_at' => '2026-05-30 14:36:56',
                                            'updated_at' => '2026-05-30 14:36:56',
                                        ),
                                        96 => 
                                        array (
                                            'id_asisten' => 485,
                                            'nama_asisten' => 'INAYA ZEHAN KALYZTA',
                                            'hari' => 'Kamis',
                                            'jam_mulai' => '09:45:00',
                                            'jam_selesai' => '11:30:00',
                                            'mata_kuliah' => 'Komunikasi Publik dan 
Presentasi
(AA)',
                                                'created_at' => '2026-05-30 14:36:56',
                                                'updated_at' => '2026-05-30 14:36:56',
                                            ),
                                            97 => 
                                            array (
                                                'id_asisten' => 486,
                                                'nama_asisten' => 'INAYA ZEHAN KALYZTA',
                                                'hari' => 'Selasa',
                                                'jam_mulai' => '10:40:00',
                                                'jam_selesai' => '13:20:00',
                                            'mata_kuliah' => 'MOPROG (AD)',
                                                'created_at' => '2026-05-30 14:36:56',
                                                'updated_at' => '2026-05-30 14:36:56',
                                            ),
                                            98 => 
                                            array (
                                                'id_asisten' => 487,
                                                'nama_asisten' => 'INAYA ZEHAN KALYZTA',
                                                'hari' => 'Rabu',
                                                'jam_mulai' => '11:35:00',
                                                'jam_selesai' => '13:20:00',
                                                'mata_kuliah' => 'Sistem Informasi 
Manajemen
(AB)',
                                                    'created_at' => '2026-05-30 14:36:56',
                                                    'updated_at' => '2026-05-30 14:36:56',
                                                ),
                                                99 => 
                                                array (
                                                    'id_asisten' => 488,
                                                    'nama_asisten' => 'INAYA ZEHAN KALYZTA',
                                                    'hari' => 'Kamis',
                                                    'jam_mulai' => '13:25:00',
                                                    'jam_selesai' => '15:10:00',
                                                    'mata_kuliah' => 'Bahasa Inggris Lanjutan 
(AE)',
                                                        'created_at' => '2026-05-30 14:36:56',
                                                        'updated_at' => '2026-05-30 14:36:56',
                                                    ),
                                                    100 => 
                                                    array (
                                                        'id_asisten' => 489,
                                                        'nama_asisten' => 'INAYA ZEHAN KALYZTA',
                                                        'hari' => 'Jumat',
                                                        'jam_mulai' => '13:25:00',
                                                        'jam_selesai' => '15:10:00',
                                                        'mata_kuliah' => 'Interpersonal Skill 
(AE)',
                                                            'created_at' => '2026-05-30 14:36:56',
                                                            'updated_at' => '2026-05-30 14:36:56',
                                                        ),
                                                        101 => 
                                                        array (
                                                            'id_asisten' => 490,
                                                            'nama_asisten' => 'INAYA ZEHAN KALYZTA',
                                                            'hari' => 'Senin',
                                                            'jam_mulai' => '13:25:00',
                                                            'jam_selesai' => '16:05:00',
                                                        'mata_kuliah' => 'SPK (AC)',
                                                            'created_at' => '2026-05-30 14:36:56',
                                                            'updated_at' => '2026-05-30 14:36:56',
                                                        ),
                                                        102 => 
                                                        array (
                                                            'id_asisten' => 491,
                                                            'nama_asisten' => 'INAYA ZEHAN KALYZTA',
                                                            'hari' => 'Selasa',
                                                            'jam_mulai' => '13:25:00',
                                                            'jam_selesai' => '16:05:00',
                                                            'mata_kuliah' => 'Arsitektur dan 
Infrastruktur IT 
(AC)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                            103 => 
                                                            array (
                                                                'id_asisten' => 492,
                                                                'nama_asisten' => 'AKTAR FAIZIL',
                                                                'hari' => 'Senin',
                                                                'jam_mulai' => '08:00:00',
                                                                'jam_selesai' => '09:40:00',
                                                            'mata_kuliah' => 'Komunikasi Publik dan Presentasi (AE)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                            104 => 
                                                            array (
                                                                'id_asisten' => 493,
                                                                'nama_asisten' => 'AKTAR FAIZIL',
                                                                'hari' => 'Jumat',
                                                                'jam_mulai' => '08:00:00',
                                                                'jam_selesai' => '09:40:00',
                                                            'mata_kuliah' => 'Etika Profesi (AB)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                            105 => 
                                                            array (
                                                                'id_asisten' => 494,
                                                                'nama_asisten' => 'AKTAR FAIZIL',
                                                                'hari' => 'Kamis',
                                                                'jam_mulai' => '08:00:00',
                                                                'jam_selesai' => '10:35:00',
                                                            'mata_kuliah' => 'PEMB (AA)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                            106 => 
                                                            array (
                                                                'id_asisten' => 495,
                                                                'nama_asisten' => 'AKTAR FAIZIL',
                                                                'hari' => 'Senin',
                                                                'jam_mulai' => '10:40:00',
                                                                'jam_selesai' => '12:26:00',
                                                            'mata_kuliah' => 'Audit SI (AA)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                            107 => 
                                                            array (
                                                                'id_asisten' => 496,
                                                                'nama_asisten' => 'AKTAR FAIZIL',
                                                                'hari' => 'Selasa',
                                                                'jam_mulai' => '10:40:00',
                                                                'jam_selesai' => '12:26:00',
                                                            'mata_kuliah' => 'Analisa Big Data (AA)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                            108 => 
                                                            array (
                                                                'id_asisten' => 497,
                                                                'nama_asisten' => 'AKTAR FAIZIL',
                                                                'hari' => 'Kamis',
                                                                'jam_mulai' => '11:35:00',
                                                                'jam_selesai' => '12:26:00',
                                                            'mata_kuliah' => 'Komputer Masyarakat (AA)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                            109 => 
                                                            array (
                                                                'id_asisten' => 498,
                                                                'nama_asisten' => 'AKTAR FAIZIL',
                                                                'hari' => 'Selasa',
                                                                'jam_mulai' => '13:25:00',
                                                                'jam_selesai' => '16:05:00',
                                                            'mata_kuliah' => 'Analisis Sentimen (AA)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                            110 => 
                                                            array (
                                                                'id_asisten' => 499,
                                                                'nama_asisten' => 'AKTAR FAIZIL',
                                                                'hari' => 'Kamis',
                                                                'jam_mulai' => '13:25:00',
                                                                'jam_selesai' => '16:05:00',
                                                            'mata_kuliah' => 'Teori Kriminologi Modern (AA)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                            111 => 
                                                            array (
                                                                'id_asisten' => 500,
                                                                'nama_asisten' => 'AKTAR FAIZIL',
                                                                'hari' => 'Jumat',
                                                                'jam_mulai' => '13:25:00',
                                                                'jam_selesai' => '16:05:00',
                                                            'mata_kuliah' => 'Medsos dan Periklanan (AA)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                            112 => 
                                                            array (
                                                                'id_asisten' => 501,
                                                                'nama_asisten' => 'MUHAMMAD RAFIF RABBANI',
                                                                'hari' => 'Selasa',
                                                                'jam_mulai' => '08:00:00',
                                                                'jam_selesai' => '09:40:00',
                                                            'mata_kuliah' => 'Analisis Teks Pada Media Sosial (AA)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                            113 => 
                                                            array (
                                                                'id_asisten' => 502,
                                                                'nama_asisten' => 'MUHAMMAD RAFIF RABBANI',
                                                                'hari' => 'Rabu',
                                                                'jam_mulai' => '08:00:00',
                                                                'jam_selesai' => '10:35:00',
                                                            'mata_kuliah' => 'Rekayasa Web (AA)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                            114 => 
                                                            array (
                                                                'id_asisten' => 503,
                                                                'nama_asisten' => 'MUHAMMAD RAFIF RABBANI',
                                                                'hari' => 'Kamis',
                                                                'jam_mulai' => '08:00:00',
                                                                'jam_selesai' => '10:35:00',
                                                            'mata_kuliah' => 'P.Ekonomi, Man., Bisn. (AA)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                            115 => 
                                                            array (
                                                                'id_asisten' => 504,
                                                                'nama_asisten' => 'MUHAMMAD RAFIF RABBANI',
                                                                'hari' => 'Selasa',
                                                                'jam_mulai' => '10:40:00',
                                                                'jam_selesai' => '12:25:00',
                                                            'mata_kuliah' => 'Aplikasi Wawasan Budi Luhur {AH)',
                                                            'created_at' => '2026-05-30 14:36:56',
                                                            'updated_at' => '2026-05-30 14:36:56',
                                                        ),
                                                        116 => 
                                                        array (
                                                            'id_asisten' => 505,
                                                            'nama_asisten' => 'MUHAMMAD RAFIF RABBANI',
                                                            'hari' => 'Rabu',
                                                            'jam_mulai' => '10:40:00',
                                                            'jam_selesai' => '12:26:00',
                                                            'mata_kuliah' => 'Bahasa Pemrograman
Python (AA)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                            117 => 
                                                            array (
                                                                'id_asisten' => 506,
                                                                'nama_asisten' => 'MUHAMMAD RAFIF RABBANI',
                                                                'hari' => 'Kamis',
                                                                'jam_mulai' => '10:40:00',
                                                                'jam_selesai' => '12:26:00',
                                                            'mata_kuliah' => 'Keamanan Komputer (AB)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                            118 => 
                                                            array (
                                                                'id_asisten' => 507,
                                                                'nama_asisten' => 'MUHAMMAD RAFIF RABBANI',
                                                                'hari' => 'Senin',
                                                                'jam_mulai' => '10:40:00',
                                                                'jam_selesai' => '14:15:00',
                                                            'mata_kuliah' => 'IPBO (AA)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                            119 => 
                                                            array (
                                                                'id_asisten' => 508,
                                                                'nama_asisten' => 'MUHAMMAD RAFIF RABBANI',
                                                                'hari' => 'Selasa',
                                                                'jam_mulai' => '13:25:00',
                                                                'jam_selesai' => '16:05:00',
                                                            'mata_kuliah' => 'Analisis Sentimen (AA)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                            120 => 
                                                            array (
                                                                'id_asisten' => 509,
                                                                'nama_asisten' => 'DAVI RIZKY MADANI',
                                                                'hari' => 'Selasa',
                                                                'jam_mulai' => '08:00:00',
                                                                'jam_selesai' => '09:40:00',
                                                            'mata_kuliah' => 'Analisis Teks Pada Media Sosial (AA)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                            121 => 
                                                            array (
                                                                'id_asisten' => 510,
                                                                'nama_asisten' => 'DAVI RIZKY MADANI',
                                                                'hari' => 'Rabu',
                                                                'jam_mulai' => '08:00:00',
                                                                'jam_selesai' => '09:40:00',
                                                            'mata_kuliah' => 'Kecerdasan Buatan Generatif (AA)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                            122 => 
                                                            array (
                                                                'id_asisten' => 511,
                                                                'nama_asisten' => 'DAVI RIZKY MADANI',
                                                                'hari' => 'Senin',
                                                                'jam_mulai' => '08:00:00',
                                                                'jam_selesai' => '10:35:00',
                                                            'mata_kuliah' => 'Pemrograman Web 1 (AG)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                            123 => 
                                                            array (
                                                                'id_asisten' => 512,
                                                                'nama_asisten' => 'DAVI RIZKY MADANI',
                                                                'hari' => 'Kamis',
                                                                'jam_mulai' => '08:00:00',
                                                                'jam_selesai' => '10:35:00',
                                                            'mata_kuliah' => 'Mobile Programming (AG)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                            124 => 
                                                            array (
                                                                'id_asisten' => 513,
                                                                'nama_asisten' => 'DAVI RIZKY MADANI',
                                                                'hari' => 'Jumat',
                                                                'jam_mulai' => '08:00:00',
                                                                'jam_selesai' => '10:35:00',
                                                            'mata_kuliah' => 'Jaringan Komputer (AE)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                            125 => 
                                                            array (
                                                                'id_asisten' => 514,
                                                                'nama_asisten' => 'DAVI RIZKY MADANI',
                                                                'hari' => 'Rabu',
                                                                'jam_mulai' => '10:40:00',
                                                                'jam_selesai' => '12:26:00',
                                                            'mata_kuliah' => 'Keamanan Siber (AC)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                            126 => 
                                                            array (
                                                                'id_asisten' => 515,
                                                                'nama_asisten' => 'DAVI RIZKY MADANI',
                                                                'hari' => 'Selasa',
                                                                'jam_mulai' => '13:25:00',
                                                                'jam_selesai' => '15:10:00',
                                                            'mata_kuliah' => 'Metodologi Riset (AH)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                            127 => 
                                                            array (
                                                                'id_asisten' => 516,
                                                                'nama_asisten' => 'DAVI RIZKY MADANI',
                                                                'hari' => 'Kamis',
                                                                'jam_mulai' => '13:25:00',
                                                                'jam_selesai' => '15:10:00',
                                                            'mata_kuliah' => 'Pembelajaran Mesin (AA)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                            128 => 
                                                            array (
                                                                'id_asisten' => 517,
                                                                'nama_asisten' => 'DAVI RIZKY MADANI',
                                                                'hari' => 'Rabu',
                                                                'jam_mulai' => '13:25:00',
                                                                'jam_selesai' => '16:05:00',
                                                            'mata_kuliah' => 'Manajemen Proyek Perangkat Lunak (AA)',
                                                                'created_at' => '2026-05-30 14:36:56',
                                                                'updated_at' => '2026-05-30 14:36:56',
                                                            ),
                                                        ));
        
        
    }
}