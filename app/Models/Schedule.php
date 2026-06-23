<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class Schedule extends Model
{
    use HasFactory;

    protected $table = 'schedules';
    protected $primaryKey = 'id_jadwal';

    protected $fillable = [
        'tanggal', 
        'hari', 
        'id_lab',       
        'jam_mulai', 
        'jam_selesai', 
        'matkul', 
        'sks', 
        'dosen'
    ];

    protected function casts(): array
    {
        return [
            'tanggal' => 'date',
        ];
    }

    public function lab(): BelongsTo
    {
        return $this->belongsTo(Lab::class, 'id_lab', 'id_lab');
    }

    /**
     * Relasi many-to-many ke AssistantSchedule via pivot table schedule_assistant.
     */
    public function assistants(): BelongsToMany
    {
        return $this->belongsToMany(
            AssistantSchedule::class,
            'schedule_assistant',
            'schedule_id',
            'assistant_schedule_id',
            'id_jadwal',
            'id_asisten'
        )->withTimestamps();
    }

    /**
     * Backward-compatible accessor: mengambil asisten pertama (untuk kode lama yang masih pakai relasi tunggal).
     * Gunakan $schedule->assistantSchedule untuk mendapatkan AssistantSchedule pertama.
     */
    public function getAssistantScheduleAttribute()
    {
        return $this->assistants->first();
    }

    /**
     * Helper: mendapatkan semua nama asisten sebagai string (dipisah koma).
     */
    public function getAssistantNamesAttribute(): string
    {
        $names = $this->assistants->pluck('nama_asisten')->unique()->toArray();
        return !empty($names) ? implode(', ', $names) : '-';
    }

    /**
     * Helper: mendapatkan semua id_asisten yang terhubung.
     */
    public function getAssistantIdsAttribute(): array
    {
        return $this->assistants->pluck('id_asisten')->toArray();
    }

    public function getLabStatuses()
    {
        $allLabs = \App\Models\Lab::where('nama_lab', '!=', 'RUANG ASISTEN')->get();
        $statuses = [];

        foreach ($allLabs as $lab) {
            $isBusy = \App\Models\Schedule::where('tanggal', $this->tanggal)
                ->where('id_jadwal', '!=', $this->id_jadwal)
                ->where('id_lab', $lab->id_lab)
                ->where(function($query) {
                    $query->where('jam_mulai', '<', $this->jam_selesai)
                          ->where('jam_selesai', '>', $this->jam_mulai);
                })->exists();

            $statuses[] = [
                'id_lab'   => $lab->id_lab,
                'nama_lab' => $lab->nama_lab,
                'status'   => $isBusy ? 'busy' : 'available'
            ];
        }

        return $statuses;
    }

    
    public function getAssistantStatuses()
    {
        $allAssistants = \App\Models\AssistantSchedule::select(DB::raw('MIN(id_asisten) as id_asisten'), 'nama_asisten')
            ->groupBy('nama_asisten')
            ->get();

        $hariTarget = $this->hari;
        $tanggalTarget = $this->tanggal;
        $mulaiTarget = $this->jam_mulai;
        $selesaiTarget = $this->jam_selesai;

        // Asisten yang sedang kuliah sendiri pada hari+jam tersebut
        $busyWithClass = \App\Models\AssistantSchedule::where('hari', $hariTarget)
            ->where(function($query) use ($mulaiTarget, $selesaiTarget) {
                $query->where('jam_mulai', '<', $selesaiTarget)
                      ->where('jam_selesai', '>', $mulaiTarget);
            })
            ->get(['nama_asisten', 'mata_kuliah'])
            ->keyBy('nama_asisten');

        // Asisten yang sedang ditugaskan ke lab lain (cek via pivot table)
        $busyInOtherLab = \App\Models\Schedule::with(['lab', 'assistants'])
            ->where('tanggal', $tanggalTarget)
            ->where('id_jadwal', '!=', $this->id_jadwal)
            ->whereHas('assistants')
            ->get()
            ->flatMap(function($s) {
                // Untuk setiap jadwal, map semua asisten yang terhubung
                return $s->assistants->map(function($asisten) use ($s) {
                    return (object) [
                        'nama_asisten' => $asisten->nama_asisten,
                        'lab' => $s->lab,
                        'schedule' => $s,
                    ];
                });
            })
            ->keyBy('nama_asisten');

        // ID asisten yang sudah ditugaskan ke jadwal ini (via pivot)
        $currentAssistantNames = $this->assistants->pluck('nama_asisten')->toArray();

        return $allAssistants->map(function ($asisten) use ($busyWithClass, $busyInOtherLab, $currentAssistantNames) {
            $status = 'available';
            $label = '';
            $namaKey = $asisten->nama_asisten;

            if ($busyWithClass->has($namaKey)) {
                $status = 'busy_class';
                $matkul = $busyWithClass->get($namaKey)->mata_kuliah;
                $label = "(Kuliah: {$matkul})";
            } 
            elseif ($busyInOtherLab->has($namaKey)) {
                $status = 'busy_lab';
                $scheduleBentrok = $busyInOtherLab->get($namaKey);
                $namaLabBentrok = $scheduleBentrok->lab->nama_lab ?? 'Lab Lain';
                $label = "(Jaga: {$namaLabBentrok})";
            }

            $idFinal = $asisten->id_asisten;
            // Jika asisten ini sudah ditugaskan ke jadwal ini, gunakan id dari relasi yang ada
            if (in_array($namaKey, $currentAssistantNames)) {
                $matchingAssistant = $this->assistants->firstWhere('nama_asisten', $namaKey);
                if ($matchingAssistant) {
                    $idFinal = $matchingAssistant->id_asisten;
                }
            }

            return (object) [
                'id_asisten' => $idFinal,
                'nama'       => $namaKey,
                'is_busy'    => ($status !== 'available'),
                'is_assigned' => in_array($namaKey, $currentAssistantNames),
                'label'      => $label
            ];
        });
    }
}