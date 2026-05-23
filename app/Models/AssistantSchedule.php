<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AssistantSchedule extends Model
{
    use HasFactory;
    
    protected $table = 'assistant_schedules';
    protected $primaryKey = 'id_asisten';
    
    protected $fillable = [
        'nama_asisten',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'mata_kuliah',
    ];

    /**
     * Relasi HasMany: Satu Asisten bisa memiliki banyak Jadwal
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'id_asisten', 'id_asisten');
    }
}
