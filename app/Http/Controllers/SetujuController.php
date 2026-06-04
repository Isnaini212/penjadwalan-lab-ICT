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
        // 1. Ambil Data Pending dari Ormawa
        $pendingOrmawa = Ormawa::where('status', 'pending')->get()->map(function($item) {
            $item->type = 'ormawa';
            $item->nama_pengaju = $item->penanggung_jawab;
            $item->identitas = $item->nama_ormawa;
            $item->kontak = $item->no_wa;
            $item->current_lab = $item->lab; // Teks: "TBD" atau "LAB 01"
            $item->current_id_lab = null;
            $item->dokumen = $item->file_surat;
            return $item;
        });

        // 2. Ambil Data Pending dari Dosen (Tarik relasi lab)
        $pendingDosen = Dosen::with('lab')->where('status', 'pending')->get()->map(function($item) {
            $item->type = 'dosen';
            $item->nama_pengaju = $item->nm_dosen;
            $item->identitas = 'Dosen / Staf';
            $item->kontak = '-';
            $item->current_lab = $item->lab ? ($item->lab->nama_lab ?? $item->lab->nm_lab) : 'Lab Dihapus';
            $item->current_id_lab = $item->id_lab;
            $item->dokumen = null; // Dosen tidak ada surat
            return $item;
        });

        // 3. Gabungkan Keduanya & Urutkan dari yang paling lama menunggu
        $bookings = $pendingOrmawa->concat($pendingDosen)->sortBy('created_at');

        // Statistik
        $totalOrmawa = $pendingOrmawa->count();
        $totalDosen = $pendingDosen->count();
        $allLabs = Lab::all();

        // 4. Kalkulasi Lab Kosong untuk Masing-masing Baris
        foreach ($bookings as $b) {
            $b->lab_options = $this->getLabAvailability($b->tanggal, $b->hari, $b->jam_mulai, $b->jam_selesai, $allLabs);
        }

        return view('spv.setuju', compact('bookings', 'totalOrmawa', 'totalDosen'));
    }

    // Fungsi Update Pilihan Lab di Dropdown
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

    // Fungsi Approve
    public function approve($type, $id)
    {
        // Mulai Transaksi Ganda (DB Transaction)
        DB::beginTransaction();

        try {
            if ($type === 'ormawa') {
                $booking = Ormawa::findOrFail($id);
                
                // Validasi pengunci: Ormawa wajib disuntik Lab dulu oleh SPV
                if ($booking->lab === 'TBD') {
                    return back()->with('error', 'Pilih ruangan Lab terlebih dahulu sebelum melakukan Approve!');
                }

                // Cari data id_lab berdasarkan nama string lab yang dipilih SPV
                // KODE YANG BENAR:
                    $lab = Lab::where('nama_lab', $booking->lab)->first();
                          
                if (!$lab) {
                    return back()->with('error', 'Ruangan Lab yang dipilih tidak terdaftar di database!');
                }

                // 🔥 ACTION 1: Copy & Insert ke tabel Schedules (Jadwal Utama)
                Schedule::create([
                    'tanggal'     => $booking->tanggal,
                    'hari'        => $booking->hari,
                    'id_lab'      => $lab->id_lab,
                    'jam_mulai'   => $booking->jam_mulai,
                    'jam_selesai' => $booking->jam_selesai,
                    'matkul'      => '[ORMAWA] ' . $booking->keperluan, // Ditandai khusus Ormawa
                    'dosen'       => $booking->nama_ormawa . ' (' . $booking->penanggung_jawab . ')',
                    'sks'         => $booking->sks ?? 1,
                    // 'id_asisten' => null (Dikosongkan dlu, nanti SPV bisa assign asisten di halaman master)
                ]);

                // ACTION 2: Update status booking ormawa menjadi approved
                $booking->update(['status' => 'approved']);

            } else {
                // FASE DOSEN
                $booking = Dosen::findOrFail($id);

                // 🔥 ACTION 1: Copy & Insert ke tabel Schedules (Jadwal Utama)
                Schedule::create([
                    'tanggal'     => $booking->tanggal,
                    'hari'        => $booking->hari,
                    'id_lab'      => $booking->id_lab, // Dosen sudah punya id_lab dari awal
                    'jam_mulai'   => $booking->jam_mulai,
                    'jam_selesai' => $booking->jam_selesai,
                    'matkul'      => '[DOSEN] ' . $booking->keperluan, // Ditandai khusus Dosen
                    'dosen'       => $booking->nm_dosen,
                    'sks'         => $booking->sks,
                ]);

                // ACTION 2: Update status booking dosen menjadi approved
                $booking->update(['status' => 'approved']);
            }

            // Jika semua langkah di atas sukses tanpa error, kunci data ke MySQL
            DB::commit();

            return back()->with('success', '🚀 Pengajuan berhasil disetujui dan otomatis sinkron ke Jadwal Utama (Schedules)!');

        } catch (\Exception $e) {
            // Jika di tengah jalan ada error (misal kolom schedules beda), batalkan semua aksi!
            DB::rollBack();
            return back()->with('error', 'Waduh gagal approve, Bre. Error: ' . $e->getMessage());
        }}

    // Fungsi Reject
    public function reject($type, $id)
    {
        if ($type === 'ormawa') {
            Ormawa::where('id_booking', $id)->update(['status' => 'rejected']);
        } else {
            Dosen::where('id_booking', $id)->update(['status' => 'rejected']);
        }

        return back()->with('success', 'Pengajuan berhasil ditolak.');
    }

    // --- HELPER: Deteksi Lab Sibuk ---
    private function getLabAvailability($tanggal, $hari, $mulai, $selesai, $allLabs)
    {
        $busySchedules = Schedule::where('hari', $hari)->where(function($q) use ($mulai, $selesai) {
            $q->where('jam_mulai', '<', $selesai)->where('jam_selesai', '>', $mulai);
        })->pluck('id_lab')->toArray();

        $busyDosen = Dosen::where('tanggal', $tanggal)->where('status', 'approved')->where(function($q) use ($mulai, $selesai) {
            $q->where('jam_mulai', '<', $selesai)->where('jam_selesai', '>', $mulai);
        })->pluck('id_lab')->toArray();

        $busyOrmawa = Ormawa::where('tanggal', $tanggal)->where('status', 'approved')->where(function($q) use ($mulai, $selesai) {
            $q->where('jam_mulai', '<', $selesai)->where('jam_selesai', '>', $mulai);
        })->pluck('lab')->toArray(); // Karena ormawa nyimpen nama string

        $allBusyLabs = array_unique(array_merge($busySchedules, $busyDosen, $busyOrmawa));

        $options = [];
        foreach ($allLabs as $lab) {
            $namaLab = $lab->nama_lab ?? $lab->nm_lab;
            $isBusy = in_array($lab->id_lab, $allBusyLabs) || in_array($namaLab, $allBusyLabs);
            
            $options[] = [
                'id_lab'   => $lab->id_lab,
                'nama_lab' => $namaLab,
                'is_busy'  => $isBusy
            ];
        }
        return $options;
    }
}