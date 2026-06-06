<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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
        'id_asisten',   
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

    public function assistantSchedule(): BelongsTo
    {
        return $this->belongsTo(AssistantSchedule::class, 'id_asisten', 'id_asisten');
    }

    public function getLabStatuses()
    {
        $allLabs = \App\Models\Lab::all();
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


        $busyWithClass = \App\Models\AssistantSchedule::where('hari', $hariTarget)
            ->where(function($query) use ($mulaiTarget, $selesaiTarget) {
                $query->where('jam_mulai', '<', $selesaiTarget)
                      ->where('jam_selesai', '>', $mulaiTarget);
            })
            ->get(['nama_asisten', 'mata_kuliah'])
            ->keyBy('nama_asisten');

        
        $busyInOtherLab = \App\Models\Schedule::with(['lab', 'assistantSchedule'])
            ->where('tanggal', $tanggalTarget)
            ->where('id_jadwal', '!=', $this->id_jadwal)
            ->whereNotNull('id_asisten')
            ->get()
            ->filter(fn($s) => $s->assistantSchedule !== null)
            ->keyBy(fn($s) => $s->assistantSchedule->nama_asisten);

        
        return $allAssistants->map(function ($asisten) use ($busyWithClass, $busyInOtherLab) {
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
            if ($this->assistantSchedule && $this->assistantSchedule->nama_asisten === $namaKey) {
                $idFinal = $this->id_asisten;
            }

            return (object) [
                'id_asisten' => $idFinal,
                'nama'       => $namaKey,
                'is_busy'    => ($status !== 'available'),
                'label'      => $label
            ];
        });
    }
}