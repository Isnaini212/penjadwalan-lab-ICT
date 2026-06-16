<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ormawa;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class MhsController extends Controller
{
    
    public function index(Request $request)
    {
        
        $myBookings = Ormawa::orderBy('created_at', 'desc')->take(10)->get();
        
        return view('booking.mahasiswa', compact('myBookings'));
    }

    
   public function store(Request $request)
{
    $request->validate([
        'penanggung_jawab' => 'required',
        'tanggal'          => 'required|date',
        'jam_mulai'        => 'required',
        'jam_selesai'      => 'required',
        'kapasitas'        => 'required|numeric',
        'keperluan'        => 'required',
        'file_surat'       => 'required|mimes:pdf|max:2048',
    ]);

    // 1.5 Validasi Kapasitas Maksimum Lab
    $max_lab_capacity = \App\Models\Lab::max('kapasitas');
    if ($max_lab_capacity && $request->kapasitas > $max_lab_capacity) {
        return back()->withInput()->withErrors(['kapasitas' => "Gagal! Kapasitas peserta ({$request->kapasitas}) melebihi kapasitas maksimum lab terbesar yang ada ({$max_lab_capacity} kursi)."]);
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
        'lab'              => 'Menunggu SPV',            
        'jam_mulai'        => $request->jam_mulai,
        'jam_selesai'      => $request->jam_selesai,
        'kapasitas'        => $request->kapasitas,
        'keperluan'        => $request->keperluan,
        'file_surat'       => $nama_file_surat, // Hanya menyimpan nama filenya saja yang bersih
        'status'           => 'pending',
    ]);

    return back()->with('success', 'Pengajuan booking laboratorium berhasil dikirim!');
}
}