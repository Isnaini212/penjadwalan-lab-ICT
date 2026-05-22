<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Schedule extends Model
{
    protected $table = 'schedules';
    protected $primaryKey = 'id_jadwal';
    
    protected $fillable = [
        'id_lab', 
        'id_asisten', 
        'tanggal', 
        'jam_mulai', 
        'jam_selesai', 
        'matkul', 
        'sks', 
        'dosen', 
        'hari'
    ];

    /**
     * Relasi BelongsTo: Sebuah Jadwal dimiliki oleh satu Lab tertentu
     * Rumus parameter: (NamaModelInduk, FK_di_tabel_ini, PK_di_tabel_induk)
     */
    public function lab(): BelongsTo
    {
        return $this->belongsTo(Lab::class, 'id_lab', 'id_lab');
    }

    /**
     * Relasi BelongsTo: Sebuah Jadwal dimiliki oleh satu Asisten tertentu
     */
    public function assistantSchedule(): BelongsTo
    {
        return $this->belongsTo(AssistantSchedule::class, 'id_asisten', 'id_asisten');
    }
}
