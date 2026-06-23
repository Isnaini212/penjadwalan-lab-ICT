<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class AssistantSchedule extends Model
{

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
     * Relasi many-to-many ke Schedule via pivot table schedule_assistant.
     */
    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(
            Schedule::class,
            'schedule_assistant',
            'assistant_schedule_id',
            'schedule_id',
            'id_asisten',
            'id_jadwal'
        )->withTimestamps();
    }
}