<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ormawa extends Model
{
    protected $table = 'booking_ormawa';
    protected $primaryKey = 'id_booking'; // Kunci utamanya

    protected $fillable = [
        'nama_ormawa',
        'penanggung_jawab',
        'tanggal',
        'hari',
        'lab',
        'jam_mulai',
        'jam_selesai',
        'kapasitas',
        'keperluan',
        'file_surat',
        'status',
    ];
}