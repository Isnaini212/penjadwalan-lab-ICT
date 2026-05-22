<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssistantSchedule extends Model
{
    protected $table = 'assistant_schedules';
    protected $primaryKey = 'id_asisten';
    
    protected $fillable = [
        'nama_asisten', 
        'jm_mulai', 
        'jm_selesai', 
        'matkul', 
        'hari_matkul'
    ];

    /**
     * Relasi HasMany: Satu Asisten bisa memiliki banyak Jadwal
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'id_asisten', 'id_asisten');
    }
}
