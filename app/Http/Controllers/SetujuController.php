<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ormawa;
use App\Models\Dosen;
use App\Models\Schedule;
use App\Models\Lab;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SetujuController extends Controller
{
    public function index()
    {
       
        $pendingOrmawa = Ormawa::with('labs')->where('status', 'pending')->get()->map(function($item) {
            $selectedLabNames = $item->labs->map(fn ($lab) => $lab->nama_lab ?? $lab->nm_lab)->filter()->values();
            $item->type = 'ormawa';
            $item->nama_pengaju = $item->penanggung_jawab;
            $item->identitas = $item->nama_ormawa;
            $item->kontak = $item->no_wa;
            $item->current_lab = $selectedLabNames->isNotEmpty() ? $selectedLabNames->implode(', ') : $item->lab;
            $item->current_id_lab = null;
            $item->current_lab_ids = $item->labs->pluck('id_lab')->map(fn ($id) => (int) $id)->toArray();
            $item->jumlah_lab = $item->jumlah_lab ?? 1;
            $item->kapasitas_per_lab = $this->getKapasitasPerLab($item->kapasitas, $item->jumlah_lab);
            $item->dokumen = $item->file_surat;
            return $item;
        });

        $pendingDosen = Dosen::with('lab')->where('status', 'pending')->get()->map(function($item) {
            $item->type = 'dosen';
            $item->nama_pengaju = $item->nm_dosen;
            $item->identitas = 'Dosen / Staf';
            $item->kontak = '-';
            $item->current_lab = $item->lab ? ($item->lab->nama_lab ?? $item->lab->nm_lab) : 'Lab Dihapus';
            $item->current_id_lab = $item->id_lab;
            $item->current_lab_ids = [$item->id_lab];
            $item->jumlah_lab = 1;
            $item->kapasitas_per_lab = $item->kapasitas;
            $item->dokumen = null; 
            return $item;
        });

        $bookings = $pendingOrmawa->concat($pendingDosen)->sortBy('created_at');

        // ==== GET HISTORY BOOKINGS ====
        $historyOrmawa = Ormawa::with('labs')->whereIn('status', ['approved', 'rejected'])->orderBy('updated_at', 'desc')->take(20)->get()->map(function($item) {
            $selectedLabNames = $item->labs->map(fn ($lab) => $lab->nama_lab ?? $lab->nm_lab)->filter()->values();
            $item->type = 'ormawa';
            $item->nama_pengaju = $item->penanggung_jawab;
            $item->identitas = $item->nama_ormawa;
            $item->kontak = $item->no_wa;
            $item->current_lab = $selectedLabNames->isNotEmpty() ? $selectedLabNames->implode(', ') : $item->lab;
            $item->current_id_lab = null;
            $item->current_lab_ids = $item->labs->pluck('id_lab')->map(fn ($id) => (int) $id)->toArray();
            $item->jumlah_lab = $item->jumlah_lab ?? 1;
            $item->kapasitas_per_lab = $this->getKapasitasPerLab($item->kapasitas, $item->jumlah_lab);
            $item->dokumen = $item->file_surat;
            return $item;
        });

        $historyDosen = Dosen::with('lab')->whereIn('status', ['approved', 'rejected'])->orderBy('updated_at', 'desc')->take(20)->get()->map(function($item) {
            $item->type = 'dosen';
            $item->nama_pengaju = $item->nm_dosen;
            $item->identitas = 'Dosen / Staf';
            $item->kontak = '-';
            $item->current_lab = $item->lab ? ($item->lab->nama_lab ?? $item->lab->nm_lab) : 'Lab Dihapus';
            $item->current_id_lab = $item->id_lab;
            $item->current_lab_ids = [$item->id_lab];
            $item->jumlah_lab = 1;
            $item->kapasitas_per_lab = $item->kapasitas;
            $item->dokumen = null; 
            return $item;
        });

        $historyBookings = $historyOrmawa->concat($historyDosen)->sortByDesc('updated_at')->take(30);

      
        $totalOrmawa = $pendingOrmawa->count();
        $totalDosen = $pendingDosen->count();
        $allLabs = Lab::all();

        
        foreach ($bookings as $b) {
            $b->lab_options = $this->getLabAvailability($b->tanggal, $b->hari, $b->jam_mulai, $b->jam_selesai, $allLabs, $b->kapasitas_per_lab);
        }

        return view('spv.setuju', compact('bookings', 'historyBookings', 'totalOrmawa', 'totalDosen'));
    }

   
    public function updateLab(Request $request, $type, $id)
    {
        if ($type === 'ormawa') {
            $booking = Ormawa::with('labs')->findOrFail($id);
            $jumlahLab = max(1, (int) ($booking->jumlah_lab ?? 1));

            $request->validate([
                'lab_ids' => 'required|array|size:' . $jumlahLab,
                'lab_ids.*' => 'required|integer|distinct|exists:labs,id_lab',
            ]);

            $labIds = array_map('intval', $request->lab_ids);
            $kapasitasPerLab = $this->getKapasitasPerLab($booking->kapasitas, $jumlahLab);
            $availableOptions = collect($this->getLabAvailability(
                $booking->tanggal,
                $booking->hari,
                $booking->jam_mulai,
                $booking->jam_selesai,
                Lab::all(),
                $kapasitasPerLab
            ))->keyBy('id_lab');

            $unavailableLabs = collect($labIds)->filter(function ($labId) use ($availableOptions) {
                return ! $availableOptions->has($labId) || $availableOptions[$labId]['is_busy'];
            });

            if ($unavailableLabs->isNotEmpty()) {
                return back()->with('error', 'Ada lab yang sudah tidak tersedia atau kapasitasnya kurang. Silakan pilih ulang lab yang tersedia.');
            }

            $labs = Lab::whereIn('id_lab', $labIds)->get()->keyBy('id_lab');
            $namaLabs = collect($labIds)->map(fn ($labId) => $labs[$labId]->nama_lab ?? $labs[$labId]->nm_lab)->implode(', ');

            $booking->labs()->sync($labIds);
            $booking->update(['lab' => $namaLabs]);

            return back()->with('success', "Lab Ormawa berhasil diatur: {$namaLabs}.");
        }

        $request->validate(['lab_id' => 'required|exists:labs,id_lab']);
        $lab = Lab::where('id_lab', $request->lab_id)->first();
        $namaLab = $lab->nama_lab ?? $lab->nm_lab;

        Dosen::where('id_booking', $id)->update(['id_lab' => $request->lab_id]);

        return back()->with('success', "Lab berhasil diubah menjadi {$namaLab}.");
    }

   
    public function approve($type, $id)
    {
        
        DB::beginTransaction();

        try {
            if ($type === 'ormawa') {
                $booking = Ormawa::with('labs')->findOrFail($id);
                
                
                if ($booking->status !== 'pending') {
                    DB::rollBack();
                    return back()->with('error', 'Pengajuan ini sudah pernah diproses.');
                }

                $jumlahLab = max(1, (int) ($booking->jumlah_lab ?? 1));
                $selectedLabs = $booking->labs;

                if ($selectedLabs->count() !== $jumlahLab) {
                    DB::rollBack();
                    return back()->with('error', "Pilih {$jumlahLab} ruangan Lab terlebih dahulu sebelum melakukan Approve!");
                }

                if ($booking->lab === 'TBD' || $booking->lab === 'Menunggu SPV') {
                    DB::rollBack();
                    return back()->with('error', 'Pilih ruangan Lab terlebih dahulu sebelum melakukan Approve!');
                }

                $kapasitasPerLab = $this->getKapasitasPerLab($booking->kapasitas, $jumlahLab);
                $availableOptions = collect($this->getLabAvailability(
                    $booking->tanggal,
                    $booking->hari,
                    $booking->jam_mulai,
                    $booking->jam_selesai,
                    Lab::all(),
                    $kapasitasPerLab
                ))->keyBy('id_lab');

                foreach ($selectedLabs as $lab) {
                    $option = $availableOptions->get($lab->id_lab);

                    if (! $option || $option['is_busy']) {
                        DB::rollBack();
                        return back()->with('error', "Gagal Approve! {$lab->nama_lab} sudah tidak tersedia atau kapasitasnya kurang.");
                    }
                }

                
                foreach ($selectedLabs as $lab) {
                    Schedule::create([
                        'tanggal'     => $booking->tanggal,
                        'hari'        => $booking->hari,
                        'id_lab'      => $lab->id_lab,
                        'jam_mulai'   => $booking->jam_mulai,
                        'jam_selesai' => $booking->jam_selesai,
                        'matkul'      => '[ORMAWA] ' . $booking->keperluan, 
                        'dosen'       => $booking->nama_ormawa . ' (' . $booking->penanggung_jawab . ')',
                        'sks'         => $booking->sks ?? 1,
                    ]);
                }

                
                $booking->update([
                    'lab' => $selectedLabs->map(fn ($lab) => $lab->nama_lab ?? $lab->nm_lab)->implode(', '),
                    'status' => 'approved',
                ]);

            } else {
                
                $booking = Dosen::findOrFail($id);

                $lab = Lab::find($booking->id_lab);
                if ($lab && $lab->kapasitas < $booking->kapasitas) {
                    DB::rollBack();
                    return back()->with('error', "Gagal Approve! Kapasitas Lab ({$lab->kapasitas}) tidak cukup untuk menampung peserta Dosen ({$booking->kapasitas}).");
                }

                Schedule::create([
                    'tanggal'     => $booking->tanggal,
                    'hari'        => $booking->hari,
                    'id_lab'      => $booking->id_lab, 
                    'jam_mulai'   => $booking->jam_mulai,
                    'jam_selesai' => $booking->jam_selesai,
                    'matkul'      => '[DOSEN] ' . $booking->keperluan, 
                    'dosen'       => $booking->nm_dosen,
                    'sks'         => $booking->sks,
                ]);

                
                $booking->update(['status' => 'approved']);
            }

            
            DB::commit();

            return back()->with('success', 'Pengajuan berhasil disetujui dan otomatis sinkron ke Jadwal Utama (Schedules)!');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Waduh gagal approve, Bre. Error: ' . $e->getMessage());
        }}

    
    public function reject($type, $id)
    {
        if ($type === 'ormawa') {
            Ormawa::where('id_booking', $id)->update(['status' => 'rejected']);
        } else {
            Dosen::where('id_booking', $id)->update(['status' => 'rejected']);
        }

        return back()->with('success', 'Pengajuan berhasil ditolak.');
    }

    
    private function getLabAvailability($tanggal, $hari, $mulai, $selesai, $allLabs, $kapasitas)
    {
        $busySchedules = Schedule::where('hari', $hari)->where(function($q) use ($mulai, $selesai) {
            $q->where('jam_mulai', '<', $selesai)->where('jam_selesai', '>', $mulai);
        })->pluck('id_lab')->toArray();

        $busyDosen = Dosen::where('tanggal', $tanggal)->where('status', 'approved')->where(function($q) use ($mulai, $selesai) {
            $q->where('jam_mulai', '<', $selesai)->where('jam_selesai', '>', $mulai);
        })->pluck('id_lab')->toArray();

        $busyOrmawa = Ormawa::where('tanggal', $tanggal)->where('status', 'approved')->where(function($q) use ($mulai, $selesai) {
            $q->where('jam_mulai', '<', $selesai)->where('jam_selesai', '>', $mulai);
        })->pluck('lab')->toArray(); 

        $busyOrmawaLabIds = DB::table('booking_ormawa_labs')
            ->join('booking_ormawa', 'booking_ormawa_labs.id_booking', '=', 'booking_ormawa.id_booking')
            ->where('booking_ormawa.tanggal', $tanggal)
            ->where('booking_ormawa.status', 'approved')
            ->where(function($q) use ($mulai, $selesai) {
                $q->where('booking_ormawa.jam_mulai', '<', $selesai)->where('booking_ormawa.jam_selesai', '>', $mulai);
            })
            ->pluck('booking_ormawa_labs.id_lab')
            ->toArray();

        $allBusyLabs = array_unique(array_merge($busySchedules, $busyDosen, $busyOrmawa, $busyOrmawaLabIds));

        $options = [];
        foreach ($allLabs as $lab) {
            $namaLab = $lab->nama_lab ?? $lab->nm_lab;
            
            // Sibuk jika jadwal tabrakan ATAU kapasitas lab kurang dari yg diminta
            $isBusy = in_array($lab->id_lab, $allBusyLabs) || in_array($namaLab, $allBusyLabs) || ($kapasitas && $lab->kapasitas < $kapasitas);
            
            $options[] = [
                'id_lab'   => $lab->id_lab,
                'nama_lab' => $namaLab,
                'is_busy'  => $isBusy
            ];
        }
        return $options;
    }

    private function getKapasitasPerLab($kapasitas, $jumlahLab): int
    {
        $jumlahLab = max(1, (int) $jumlahLab);

        return min(36, (int) ceil($kapasitas / $jumlahLab));
    }
}
