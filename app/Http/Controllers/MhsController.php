<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ormawa;
use App\Models\Schedule;
use App\Models\Lab;
use App\Models\Dosen;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MhsController extends Controller
{

    public function index(Request $request)
    {
        $myBookings = Ormawa::where('user_id', auth()->id())->orderBy('created_at', 'desc')->take(20)->get();

        return view('booking.mahasiswa', compact('myBookings'));
    }


   public function store(Request $request)
{
    $request->validate([
        'penanggung_jawab' => 'required',
        'tanggal'          => 'required|date',
        'jam_mulai'        => 'required',
        'jam_selesai'      => 'required',
        'kapasitas'        => 'required|integer|min:1',
        'jumlah_lab'       => 'required|integer|min:1',
        'keperluan'        => 'required',
        'file_surat'       => 'required|mimes:pdf|max:2048',
    ]);

    // Validasi Tanggal & Jam tidak boleh berlalu (masa lalu)
    $bookingDateTime = Carbon::parse($request->tanggal . ' ' . $request->jam_mulai);
    if ($bookingDateTime->isPast()) {
        return back()->withInput()->withErrors(['tanggal' => 'Gagal! Tanggal dan jam peminjaman tidak boleh di masa lalu.']);
    }

    // 1.5 Validasi kebutuhan lab Ormawa: standar maksimal 36 peserta per lab.
    $maksimalPesertaPerLab = 36;
    $minimalLab = (int) ceil($request->kapasitas / $maksimalPesertaPerLab);

    if ((int) $request->jumlah_lab < $minimalLab) {
        return back()->withInput()->withErrors([
            'jumlah_lab' => "Jumlah lab minimal {$minimalLab} untuk {$request->kapasitas} peserta dengan standar {$maksimalPesertaPerLab} peserta per lab.",
        ]);
    }

    // Hitung lab yang benar-benar tersedia saat itu
    $mulai = $request->jam_mulai;
    $selesai = $request->jam_selesai;
    $tanggal = $request->tanggal;
    $hari = Carbon::parse($tanggal)->locale('id')->translatedFormat('l');

    // 1. Cek schedule kuliah (berdasarkan HARI)
    $busySchedules = Schedule::where('hari', $hari)
        ->where(function($q) use ($mulai, $selesai) {
            $q->where('jam_mulai', '<', $selesai)
              ->where('jam_selesai', '>', $mulai);
        })->pluck('id_lab')->toArray();

    // 2. Cek approved Dosen booking (berdasarkan TANGGAL)
    $busyDosen = Dosen::where('tanggal', $tanggal)
        ->where('status', 'approved')
        ->where(function($q) use ($mulai, $selesai) {
            $q->where('jam_mulai', '<', $selesai)
              ->where('jam_selesai', '>', $mulai);
        })->pluck('id_lab')->toArray();

    // 3. Cek approved Ormawa booking (berdasarkan TANGGAL)
    $busyOrmawaLabIds = DB::table('booking_ormawa_labs')
        ->join('booking_ormawa', 'booking_ormawa_labs.id_booking', '=', 'booking_ormawa.id_booking')
        ->where('booking_ormawa.tanggal', $tanggal)
        ->where('booking_ormawa.status', 'approved')
        ->where(function($q) use ($mulai, $selesai) {
            $q->where('booking_ormawa.jam_mulai', '<', $selesai)
              ->where('booking_ormawa.jam_selesai', '>', $mulai);
        })
        ->pluck('booking_ormawa_labs.id_lab')
        ->toArray();

    $busyOrmawaNames = Ormawa::where('tanggal', $tanggal)
        ->where('status', 'approved')
        ->where(function($q) use ($mulai, $selesai) {
            $q->where('jam_mulai', '<', $selesai)
              ->where('jam_selesai', '>', $mulai);
        })->pluck('lab')->toArray();

    $allBusyLabs = array_unique(array_merge($busySchedules, $busyDosen, $busyOrmawaLabIds));
    $allLabs = Lab::where('nama_lab', 'like', '%LAB%')->get();

    $jumlahLabTersedia = 0;
    foreach ($allLabs as $lab) {
        $namaLab = $lab->nama_lab ?? $lab->nm_lab;
        $isBusy = in_array($lab->id_lab, $allBusyLabs) || in_array($namaLab, $busyOrmawaNames);
        if (!$isBusy) {
            $jumlahLabTersedia++;
        }
    }

    if ((int) $request->jumlah_lab > $jumlahLabTersedia) {
        return back()->withInput()->withErrors([
            'jumlah_lab' => "Jumlah lab yang diminta melebihi jumlah lab tersedia pada waktu tersebut (Tersedia: {$jumlahLabTersedia} lab).",
        ]);
    }

    // 2. Format Hari Otomatis
    $hari_otomatis = Carbon::parse($request->tanggal)->locale('id')->isoFormat('dddd');

    $nama_file_surat = null;

    // 3. Proses Upload File
    if ($request->hasFile('file_surat')) {
        $file = $request->file('file_surat');

        // PENTING: Menghapus spasi DAN garis miring (/) agar tidak merusak URL/Path
        $nama_user_bersih = str_replace([' ', '/', '\\'], '_', auth()->user()->name);

        // Hasilnya akan rapi seperti: BEM___Ormawa_1781061516.pdf
        $nama_file_surat = $nama_user_bersih . '_' . time() . '.' . $file->getClientOriginalExtension();

        // Pindahkan langsung ke folder public utama
        $file->move(public_path('surat_ormawa'), $nama_file_surat);
    }

    // 4. Simpan ke Database via Eloquent
    Ormawa::create([
        'user_id'          => auth()->id(),
        'nama_ormawa'      => auth()->user()->name,
        'penanggung_jawab' => $request->penanggung_jawab,
        'tanggal'          => $request->tanggal,
        'hari'             => $hari_otomatis,
        'lab'              => 'Belum ditentukan',
        'jam_mulai'        => $request->jam_mulai,
        'jam_selesai'      => $request->jam_selesai,
        'kapasitas'        => $request->kapasitas,
        'jumlah_lab'       => $request->jumlah_lab,
        'keperluan'        => $request->keperluan,
        'file_surat'       => $nama_file_surat, // Hanya menyimpan nama filenya saja yang bersih
        'status'           => 'pending',
    ]);

    return back()->with('success', 'Pengajuan booking laboratorium berhasil dikirim!');
}

    public function destroy($id)
    {
        $booking = Ormawa::findOrFail($id);

        // Pastikan hanya pemilik yang bisa menghapus
        if ($booking->user_id !== auth()->id()) {
            return back()->with('error', 'Anda tidak memiliki akses untuk menghapus pengajuan ini.');
        }

        // Jangan izinkan hapus jika status approved
        if ($booking->status === 'approved') {
            return back()->with('error', 'Gagal! Pengajuan yang sudah disetujui tidak dapat dihapus.');
        }

        // Hapus file surat jika ada
        if ($booking->file_surat) {
            $filePath = public_path('surat_ormawa/' . $booking->file_surat);
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }

        $booking->delete();

        return back()->with('success', 'Pengajuan booking berhasil dihapus.');
    }

    public function checkAvailableLabsCount(Request $request)
    {
        $request->validate([
            'tanggal'     => 'required|date',
            'jam_mulai'   => 'required',
            'jam_selesai' => 'required',
        ]);

        $tanggal = $request->tanggal;
        $mulai = $request->jam_mulai;
        $selesai = $request->jam_selesai;

        Carbon::setLocale('id');
        $hari = Carbon::parse($tanggal)->translatedFormat('l');

        // 1. Cek schedule kuliah (berdasarkan HARI)
        $busySchedules = Schedule::where('hari', $hari)
            ->where(function($q) use ($mulai, $selesai) {
                $q->where('jam_mulai', '<', $selesai)
                  ->where('jam_selesai', '>', $mulai);
            })->pluck('id_lab')->toArray();

        // 2. Cek approved Dosen booking (berdasarkan TANGGAL)
        $busyDosen = Dosen::where('tanggal', $tanggal)
            ->where('status', 'approved')
            ->where(function($q) use ($mulai, $selesai) {
                $q->where('jam_mulai', '<', $selesai)
                  ->where('jam_selesai', '>', $mulai);
            })->pluck('id_lab')->toArray();

        // 3. Cek approved Ormawa booking (berdasarkan TANGGAL)
        $busyOrmawaLabIds = DB::table('booking_ormawa_labs')
            ->join('booking_ormawa', 'booking_ormawa_labs.id_booking', '=', 'booking_ormawa.id_booking')
            ->where('booking_ormawa.tanggal', $tanggal)
            ->where('booking_ormawa.status', 'approved')
            ->where(function($q) use ($mulai, $selesai) {
                $q->where('booking_ormawa.jam_mulai', '<', $selesai)
                  ->where('booking_ormawa.jam_selesai', '>', $mulai);
            })
            ->pluck('booking_ormawa_labs.id_lab')
            ->toArray();

        $busyOrmawaNames = Ormawa::where('tanggal', $tanggal)
            ->where('status', 'approved')
            ->where(function($q) use ($mulai, $selesai) {
                $q->where('jam_mulai', '<', $selesai)
                  ->where('jam_selesai', '>', $mulai);
            })->pluck('lab')->toArray();

        $allBusyLabs = array_unique(array_merge($busySchedules, $busyDosen, $busyOrmawaLabIds));
        $allLabs = Lab::where('nama_lab', 'like', '%LAB%')->get();

        $availableLabsCount = 0;
        foreach ($allLabs as $lab) {
            $namaLab = $lab->nama_lab ?? $lab->nm_lab;
            $isBusy = in_array($lab->id_lab, $allBusyLabs) || in_array($namaLab, $busyOrmawaNames);
            if (!$isBusy) {
                $availableLabsCount++;
            }
        }

        return response()->json([
            'available_labs_count' => $availableLabsCount
        ]);
    }
}
