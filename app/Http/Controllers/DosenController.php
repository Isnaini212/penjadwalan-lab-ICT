<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dosen;
use App\Models\Ormawa;
use App\Models\Schedule;
use App\Models\Lab; 
use Carbon\Carbon;

class DosenController extends Controller
{
    public function index()
    {
        // Panggil relasi 'lab' biar di riwayat tabel bisa muncul nama lab-nya
        $myBookings = Dosen::with('lab')->orderBy('created_at', 'desc')->take(10)->get();
        return view('booking.dosen', compact('myBookings'));
    }

    // AJAX: Cek Lab Kosong + Ambil Fasilitas
    public function checkAvailableLabs(Request $request)
    {
        $tanggal = $request->tanggal;
        $mulai = $request->jam_mulai;
        $sks = $request->sks;

        $jamSelesai = Carbon::createFromFormat('H:i', $mulai)->addMinutes($sks * 50)->format('H:i');

        Carbon::setLocale('id');
        $hari = Carbon::parse($tanggal)->translatedFormat('l');

        // Cari Lab Sibuk di Schedules (Jadwal Tetap)
        $busySchedules = Schedule::where('hari', $hari)
            ->where(function($q) use ($mulai, $jamSelesai) {
                $q->where('jam_mulai', '<', $jamSelesai)
                  ->where('jam_selesai', '>', $mulai);
            })->pluck('id_lab')->toArray();

        // Cari Lab Sibuk di Booking Dosen
        $busyDosen = Dosen::where('tanggal', $tanggal)->where('status', 'approved')
            ->where(function($q) use ($mulai, $jamSelesai) {
                $q->where('jam_mulai', '<', $jamSelesai)->where('jam_selesai', '>', $mulai);
            })->pluck('id_lab')->toArray();

        // 🔥 PERBAIKAN: Tabel Ormawa pakai kolom 'lab', bukan 'id_lab'
        $busyOrmawa = Ormawa::where('tanggal', $tanggal)->where('status', 'approved')
            ->where(function($q) use ($mulai, $jamSelesai) {
                $q->where('jam_mulai', '<', $jamSelesai)->where('jam_selesai', '>', $mulai);
            })->pluck('lab')->toArray();

        $allBusyLabs = array_unique(array_merge($busySchedules, $busyDosen, $busyOrmawa));
        $allLabs = Lab::all(); 

        $response = [];
        foreach ($allLabs as $lab) {
            // Deteksi bentrok berdasarkan id_lab
            $isBusy = in_array($lab->id_lab, $allBusyLabs);
            
            $response[] = [
                'id_lab'    => $lab->id_lab, 
                'nama_lab'  => $lab->nama_lab ?? $lab->nm_lab,
                'fasilitas' => $lab->fasilitas ?? 'Tidak ada fasilitas khusus', 
                'is_busy'   => $isBusy
            ];
        }

        return response()->json([
            'jam_selesai' => $jamSelesai,
            'labs' => $response
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nm_dosen'  => 'required|string|max:255',
            'tanggal'   => 'required|date',
            'jam_mulai' => 'required|string|size:5',
            'sks'       => 'required|integer|min:1',
            'id_lab'    => 'required|exists:labs,id_lab', 
            'kapasitas' => 'required|integer|min:1',
            'keperluan' => 'required|string',
        ]);

        Carbon::setLocale('id');
        $hari = Carbon::parse($request->tanggal)->translatedFormat('l');
        $jamSelesai = Carbon::createFromFormat('H:i', $request->jam_mulai)->addMinutes($request->sks * 50)->format('H:i');

        try {
            Dosen::create([
                'nm_dosen'    => ucwords($request->nm_dosen),
                'tanggal'     => $request->tanggal,
                'hari'        => $hari,
                'id_lab'      => $request->id_lab, 
                'jam_mulai'   => $request->jam_mulai,
                'jam_selesai' => $jamSelesai,
                'kapasitas'   => $request->kapasitas,
                'keperluan'   => $request->keperluan,
                'sks'         => $request->sks,
                'status'      => 'pending',
            ]);

            return back()->with('success', 'Pengajuan Booking Lab berhasil dikirim ke SPV!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }
}