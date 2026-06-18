<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dosen;
use App\Models\Ormawa;
use App\Models\Schedule;
use App\Models\Lab; 
use App\Models\User; 
use Carbon\Carbon;

class DosenController extends Controller
{
    public function index()
    {
        
        $myBookings = Dosen::with('lab')->orderBy('created_at', 'desc')->take(10)->get();
        return view('booking.dosen', compact('myBookings'));
    }

    
    public function checkAvailableLabs(Request $request)
    {
        $tanggal = $request->tanggal;
        $mulai = $request->jam_mulai;
        $sks = $request->sks;
<<<<<<< HEAD
        
=======
        $kapasitas = $request->kapasitas;
>>>>>>> c63b3b29136b0a8f9d3c3b5faaf0960fe9f3f637

        $jamSelesai = Carbon::createFromFormat('H:i', $mulai)->addMinutes($sks * 53.3334)->format('H:i');

        Carbon::setLocale('id');
        $hari = Carbon::parse($tanggal)->translatedFormat('l');

        
        $busySchedules = Schedule::where('hari', $hari)
            ->where(function($q) use ($mulai, $jamSelesai) {
                $q->where('jam_mulai', '<', $jamSelesai)
                  ->where('jam_selesai', '>', $mulai);
            })->pluck('id_lab')->toArray();

        
        $busyDosen = Dosen::where('tanggal', $tanggal)->where('status', 'approved')
            ->where(function($q) use ($mulai, $jamSelesai) {
                $q->where('jam_mulai', '<', $jamSelesai)->where('jam_selesai', '>', $mulai);
            })->pluck('id_lab')->toArray();

        
        $busyOrmawa = Ormawa::where('tanggal', $tanggal)->where('status', 'approved')
            ->where(function($q) use ($mulai, $jamSelesai) {
                $q->where('jam_mulai', '<', $jamSelesai)->where('jam_selesai', '>', $mulai);
            })->pluck('lab')->toArray();

        $allBusyLabs = array_unique(array_merge($busySchedules, $busyDosen, $busyOrmawa));
        $allLabs = Lab::all(); 

        $response = [];
        foreach ($allLabs as $lab) {
            
            // Lab sibuk JIKA sudah dipesan ATAU kapasitas lab kurang dari kapasitas yang diminta
            $isBusy = in_array($lab->id_lab, $allBusyLabs) || ($kapasitas && $lab->kapasitas < $kapasitas);
            
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
        'nm_dosen'  => 'required',
        'tanggal'   => 'required',
        'jam_mulai' => 'required',
        'sks'       => 'required|numeric',
        'id_lab'    => 'required',
        'kapasitas' => 'required|numeric',
    ]);

    // Cek kapasitas lab di backend
    $lab = Lab::find($request->id_lab);
    if ($lab && $request->kapasitas > $lab->kapasitas) {
        return back()->with('error', "Gagal! Kapasitas peserta ({$request->kapasitas}) melebihi kapasitas {$lab->nama_lab} ({$lab->kapasitas} kursi).");
    }

    
    $hari_otomatis = Carbon::parse($request->tanggal)->locale('id')->isoFormat('dddd');

    
    
    $total_menit = $request->sks * 53.3334; 
    $jam_selesai_otomatis = Carbon::createFromFormat('H:i', $request->jam_mulai)
                                  ->addMinutes($total_menit)
                                  ->format('H:i');

    
    Dosen::create([
        'user_id'     => auth()->id(),
        'nm_dosen'    => $request->nm_dosen,
        'tanggal'     => $request->tanggal,
        
        'hari'        => $hari_otomatis,        
        'jam_selesai' => $jam_selesai_otomatis,  
        
        'id_lab'      => $request->id_lab,
        'jam_mulai'   => $request->jam_mulai,
        'kapasitas'   => $request->kapasitas,
        'keperluan'   => $request->keperluan,
        'sks'         => $request->sks,
        'status'      => 'pending',
    ]);

    return redirect()->back()->with('success', 'Reservasi laboratorium berhasil dikirim!');
}
    

          
    
}