<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Lab extends Model
{
    use HasFactory;

    protected $table = 'labs';
    protected $primaryKey = 'id_lab';
    
    protected $fillable = ['nm_lab', 'kapasitas', 'fasilitas'];

    /**
     * Relasi HasMany: Satu Lab bisa memiliki banyak Jadwal
     * Rumus parameter: (NamaModelTarget, FK_di_tabel_target, PK_di_tabel_ini)
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'id_lab', 'id_lab');
    }
}
