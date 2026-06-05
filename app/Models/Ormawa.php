<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ormawa extends Model
{
    protected $table = 'booking_ormawa';
    protected $primaryKey = 'id_booking'; // Kunci utamanya

    protected $fillable = [
        'user_id',
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

    // Relasi balik ke model user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}