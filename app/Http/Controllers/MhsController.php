<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ormawa;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MhsController extends Controller
{
    // Tampilkan Halaman Index Ormawa
    public function index()
    {
        // Ambil 10 riwayat pengajuan terbaru untuk ditampilkan di tabel bawah
        $myBookings = Ormawa::orderBy('created_at', 'desc')->take(10)->get();
        
        return view('booking.mahasiswa', compact('myBookings'));
    }

    // Proses Form Submit
    public function store(Request $request)
    {
        // 1. Validasi Input (Surat PDF Wajib)
        $request->validate([
            'nama_ormawa'      => 'required|string|max:255',
            'penanggung_jawab' => 'required|string|max:255',
            'tanggal'          => 'required|date',
            'jam_mulai'        => 'required|string|size:5',
            'jam_selesai'      => 'required|string|size:5',
            'kapasitas'        => 'required|integer|min:1',
            'keperluan'        => 'required|string',
            'file_surat'       => 'required|mimes:pdf|max:2048',
        ]);

        // 2. Upload Surat ke folder 'storage/app/public/surat-booking'
        $path = null;
        if ($request->hasFile('file_surat')) {
            $path = $request->file('file_surat')->store('surat-booking', 'public');
        }

        // 3. Konversi Tanggal jadi Nama Hari (Bahasa Indonesia)
        Carbon::setLocale('id');
        $hari = Carbon::parse($request->tanggal)->translatedFormat('l');

        DB::beginTransaction();
        try {
            // 4. Simpan ke database
            Ormawa::create([
                'nama_ormawa'      => strtoupper($request->nama_ormawa),
                'penanggung_jawab' => ucwords($request->penanggung_jawab),
                'tanggal'          => $request->tanggal,
                'hari'             => $hari,
                'lab'              => 'TBD', // Lab belum ditentukan (Tunggu SPV)
                'jam_mulai'        => $request->jam_mulai,
                'jam_selesai'      => $request->jam_selesai,
                'kapasitas'        => $request->kapasitas,
                'keperluan'        => $request->keperluan,
                'file_surat'       => $path,
                'status'           => 'pending',
            ]);

            DB::commit();

            return back()->with('success', '🚀 Pengajuan Booking berhasil dikirim! Silakan tunggu konfirmasi selanjutnya dari SPV.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Waduh gagal menyimpan pengajuan: ' . $e->getMessage());
        }
    }
}