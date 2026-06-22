<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Ormawa extends Model
{
    protected $table = 'booking_ormawa';
    protected $primaryKey = 'id_booking'; 

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
        'jumlah_lab',
        'keperluan',
        'file_surat',
        'status',
        'alasan_penolakan',
        'alasan_perubahan',
    ];

    
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function labs(): BelongsToMany
    {
        return $this->belongsToMany(Lab::class, 'booking_ormawa_labs', 'id_booking', 'id_lab', 'id_booking', 'id_lab')
            ->withTimestamps();
    }
}
