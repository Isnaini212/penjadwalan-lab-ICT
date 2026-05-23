<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Schedule;


class JadwalController extends Controller
{
    public function index ()
    { $jadwal = Schedule::orderBy('id_lab', 'asc')->get();
    return view('spv.jadwal', compact('jadwal'));}
 
    
    public function dbdepan()
    {
        $jadwal = Schedule::orderBy('id_lab', 'asc')->get();
        return view('spv.dashboard', compact('jadwal'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_lab'       => 'required|exists:labs,id_lab',
            'tanggal' => 'required|date',
            'lab' => 'required',
            'jam_mulai' => 'required',
            'matkul' => 'required',
            'sks' => 'required|numeric',
            'dosen' => 'required',
            'nama_asisten' => 'required'
        ]);

        $menit =$request->sks * 50;
        $hari = Carbon::parse($request->tanggal)->locale('id')->translatedFormat('l');
        $jam_selesai = Carbon::parse($request->jam_mulai)->addMinutes($menit)->format('H:i');

        Schedule::create([
            'tanggal' => $request->input('tanggal'),
            'hari' => $hari,
            'lab' => $request->lab,
            'jam_mulai' => $request->input('jam_mulai'),
            'jam_selesai' =>  $jam_selesai,
            'matkul' => $request->input('matkul'),
            'sks' => $request->input('sks'),
            'dosen' => $request->input('dosen'),
            'nama_asisten' => $request->input('nama_asisten')
        ]);
        return redirect()->route('spv.jadwal')->with('success', 'Data berhasil ditambahkan!');
    
    }

    public function editJadwal($id_jadwal)
    {
    $jadwal = Schedule::orderBy('id_lab', 'desc')->get();
    $editJadwal = Schedule::findOrFail($id_jadwal);



    return view('spv.jadwal', compact('editJadwal', 'jadwal'));

    }

    public function updateJadwal(Request $request, $id_jadwal)
    {
        $jadwal = Schedule::findOrFail($id_jadwal);

        $request->validate([
            'tanggal' => 'required|date',
            'hari' => 'required',
            'lab' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'matkul' => 'required',
            'sks' => 'required|numeric',
            'dosen' => 'required',
            'nama_asisten' => 'required'
        ]);

        $jadwal->update([
            'tanggal' => $request->input('tanggal'),
            'hari'   => $request->tanggal ? \Carbon\Carbon::parse($request->tanggal)->locale('id')->translatedFormat('l') : $schedule->hari,
            'lab' => $request->input('lab'),
            'jam_mulai' => $request->input('jam_mulai'),
            'jam_selesai' => $request->input('jam_selesai'),
            'matkul' => $request->input('matkul'),
            'sks' => $request->input('sks'),
            'dosen' => $request->input('dosen'),
            'nama_asisten' => $request->input('nama_asisten')
        ]);

        return redirect ()->route('spv.jadwal')->with('success', 'Data berhasil diperbarui!');

    }

    public function destroy($id_jadwal)
    {
        $jadwal = Schedule::findOrFail($id_jadwal);
        $jadwal->delete();

        return redirect()->route('spv.jadwal')->with('success', 'Data berhasil dihapus!');
    }
}
