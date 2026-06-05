<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Dosen extends Model
{
    protected $table = 'booking_dosen';
    protected $primaryKey = 'id_booking';

    protected $fillable = [
        'user_id',
        'nm_dosen',
        'tanggal',
        'hari',
        'id_lab',
        'jam_mulai',
        'jam_selesai',
        'kapasitas',
        'keperluan',
        'sks',
        'status',
    ];

    // Relasi balik ke model user
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    // Relasi balik ke model Lab
    public function lab(): BelongsTo
    {
        return $this->belongsTo(Lab::class, 'id_lab', 'id_lab');
    }
}
