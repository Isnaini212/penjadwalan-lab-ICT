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
       
        $pendingOrmawa = Ormawa::where('status', 'pending')->get()->map(function($item) {
            $item->type = 'ormawa';
            $item->nama_pengaju = $item->penanggung_jawab;
            $item->identitas = $item->nama_ormawa;
            $item->kontak = $item->no_wa;
            $item->current_lab = $item->lab; 
            $item->current_id_lab = null;
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
            $item->dokumen = null; 
            return $item;
        });

        $bookings = $pendingOrmawa->concat($pendingDosen)->sortBy('created_at');

        // ==== GET HISTORY BOOKINGS ====
        $historyOrmawa = Ormawa::whereIn('status', ['approved', 'rejected'])->orderBy('updated_at', 'desc')->take(20)->get()->map(function($item) {
            $item->type = 'ormawa';
            $item->nama_pengaju = $item->penanggung_jawab;
            $item->identitas = $item->nama_ormawa;
            $item->kontak = $item->no_wa;
            $item->current_lab = $item->lab; 
            $item->current_id_lab = null;
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
            $item->dokumen = null; 
            return $item;
        });

        $historyBookings = $historyOrmawa->concat($historyDosen)->sortByDesc('updated_at')->take(30);

      
        $totalOrmawa = $pendingOrmawa->count();
        $totalDosen = $pendingDosen->count();
        $allLabs = Lab::all();

        
        foreach ($bookings as $b) {
            $b->lab_options = $this->getLabAvailability($b->tanggal, $b->hari, $b->jam_mulai, $b->jam_selesai, $allLabs, $b->kapasitas);
        }

        return view('spv.setuju', compact('bookings', 'historyBookings', 'totalOrmawa', 'totalDosen'));
    }

   
    public function updateLab(Request $request, $type, $id)
    {
        $request->validate(['lab_id' => 'required|exists:labs,id_lab']);
        $lab = Lab::where('id_lab', $request->lab_id)->first();
        $namaLab = $lab->nama_lab ?? $lab->nm_lab;

        if ($type === 'ormawa') {
            Ormawa::where('id_booking', $id)->update(['lab' => $namaLab]);
        } else {
            Dosen::where('id_booking', $id)->update(['id_lab' => $request->lab_id]);
        }

        return back()->with('success', "Lab berhasil diubah menjadi {$namaLab}.");
    }

   
    public function approve($type, $id)
    {
        
        DB::beginTransaction();

        try {
            if ($type === 'ormawa') {
                $booking = Ormawa::findOrFail($id);
                
                
                if ($booking->lab === 'TBD') {
                    return back()->with('error', 'Pilih ruangan Lab terlebih dahulu sebelum melakukan Approve!');
                }

                    $lab = Lab::where('nama_lab', $booking->lab)->first();
                          
                if (!$lab) {
                    return back()->with('error', 'Ruangan Lab yang dipilih tidak terdaftar di database!');
                }

                if ($lab->kapasitas < $booking->kapasitas) {
                    return back()->with('error', "Gagal Approve! Kapasitas {$lab->nama_lab} ({$lab->kapasitas}) tidak cukup untuk menampung peserta Ormawa ({$booking->kapasitas}).");
                }

                
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

                
                $booking->update(['status' => 'approved']);

            } else {
                
                $booking = Dosen::findOrFail($id);

                $lab = Lab::find($booking->id_lab);
                if ($lab && $lab->kapasitas < $booking->kapasitas) {
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

    
    public function reject(Request $request, $type, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'nullable|string|max:500',
        ]);

        $alasan = trim($request->alasan_penolakan ?? 'Tidak ada keterangan.');

        if ($type === 'ormawa') {
            Ormawa::where('id_booking', $id)->update([
                'status' => 'rejected',
                'alasan_penolakan' => $alasan,
            ]);
        } else {
            Dosen::where('id_booking', $id)->update([
                'status' => 'rejected',
                'alasan_penolakan' => $alasan,
            ]);
        }

        return back()->with('success', 'Pengajuan berhasil ditolak.');
    }

    public function updateRejectionReason(Request $request, $type, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'nullable|string|max:500',
        ]);

        $alasan = trim($request->alasan_penolakan ?? '');

        if ($type === 'ormawa') {
            Ormawa::where('id_booking', $id)
                ->where('status', 'rejected')
                ->update(['alasan_penolakan' => $alasan]);
        } else {
            Dosen::where('id_booking', $id)
                ->where('status', 'rejected')
                ->update(['alasan_penolakan' => $alasan]);
        }

        return back()->with('success', 'Alasan penolakan berhasil diperbarui.');
    }

    public function bulkDeleteHistory(Request $request)
    {
        $ids = $request->input('selected_ids', []);
        $types = $request->input('selected_types', []);

        if (empty($ids)) {
            return back()->with('error', 'Tidak ada item yang dipilih untuk dihapus.');
        }

        $dosenIds = [];
        $ormawaIds = [];

        foreach ($ids as $index => $id) {
            $type = $types[$index] ?? null;
            if ($type === 'dosen') {
                $dosenIds[] = $id;
            } elseif ($type === 'ormawa') {
                $ormawaIds[] = $id;
            }
        }

        if (!empty($dosenIds)) {
            Dosen::whereIn('id_booking', $dosenIds)
                ->whereIn('status', ['approved', 'rejected'])
                ->delete();
        }

        if (!empty($ormawaIds)) {
            Ormawa::whereIn('id_booking', $ormawaIds)
                ->whereIn('status', ['approved', 'rejected'])
                ->delete();
        }

        $totalDeleted = count($dosenIds) + count($ormawaIds);
        return back()->with('success', "Berhasil menghapus {$totalDeleted} riwayat booking.");
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
        $allBusyLabs = array_unique(array_merge($busySchedules, $busyDosen, $busyOrmawa));

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
}