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

        
        $hari_otomatis = Carbon::parse($request->tanggal)->locale('id')->isoFormat('dddd');


        $nama_file_surat = null;
        if ($request->hasFile('file_surat')) {
            $file = $request->file('file_surat');
            
            $nama_file_surat = str_replace(' ', '_', auth()->user()->name) . '_' . time() . '.' . $file->getClientOriginalExtension();
            
            
            $file->storeAs('public/surat_ormawa', $nama_file_surat);
        }

        
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
            'file_surat'       => $nama_file_surat,         
            'status'           => 'pending',
        ]);

        return back()->with('success', 'Pengajuan booking laboratorium berhasil dikirim!');
    }
}