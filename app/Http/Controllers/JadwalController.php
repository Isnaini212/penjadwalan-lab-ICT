<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\Booking; 
use App\Models\AssistantSchedule;
use App\Models\Lab;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class JadwalController extends Controller
{



    public function welcome(Request $request)
    {
        $filterDate = $request->query('filter_date', now()->toDateString());

        $schedules = Schedule::with(['lab', 'assistantSchedule'])
                         ->whereDate('tanggal', $filterDate)
                         ->whereHas('lab', function($query) {
                             $query->where('nama_lab', '!=', 'RUANG RA')
                                   ->where('nama_lab', '!=', 'RA');
                         })
                         ->orderBy('jam_mulai', 'asc')
                         ->get();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($schedules);
        }

        return view('welcome', compact('schedules', 'filterDate'));
    }

    public function manajemenJadwal(Request $request) {
        $filterDate = $request->query('filter_date', now()->toDateString());
        $schedules = Schedule::with(['lab', 'assistantSchedule'])->whereDate('tanggal', $filterDate)->orderBy('jam_mulai', 'asc')->get();
        $labs = Lab::all();
        return view('spv.jadwal', compact('schedules', 'labs','filterDate'));
    }
    public function store(Request $request)
    {
        $request->validate([
            'tanggal'   => 'required|date',
            'id_lab'    => 'nullable',
            'lab'       => 'nullable',
            'jam_mulai' => 'required',
            'matkul'    => 'required',
            'sks'       => 'required|numeric',
            'dosen'     => 'required',
        ]);

        $menit = $request->sks * 50;
        $jam_selesai = date('H:i', strtotime($request->jam_mulai . " + $menit minutes"));
        $hari = Carbon::parse($request->tanggal)->locale('id')->translatedFormat('l');

        $id_lab = $request->id_lab;
        if (!$id_lab && $request->lab) {
            $labObj = Lab::firstOrCreate(['nama_lab' => strtoupper($request->lab)], ['kapasitas' => 40, 'fasilitas' => '-']);
            $id_lab = $labObj->id_lab;
        }

        $id_asisten = $request->id_asisten ?: null;

        Schedule::create([
            'tanggal'        => $request->tanggal,
            'hari'           => $hari,
            'id_lab'         => $id_lab,
            'id_asisten'     => $id_asisten,
            'jam_mulai'      => $request->jam_mulai,
            'jam_selesai'    => $jam_selesai,
            'matkul'         => $request->matkul,
            'sks'            => $request->sks,
            'dosen'          => $request->dosen,
        ]);

        return back()->with('success', 'Jadwal berhasil ditambahkan!');
    }


    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();
        return back()->with('success', 'Jadwal dihapus!');
    }


    public function reject($id)
    {
        try {
            $booking = Booking::findOrFail($id);
            $booking->update(['status' => 'rejected']);
            return back()->with('error', 'SYSTEM_MSG: Pengajuan telah ditolak!');
        } catch (\Exception $e) {
            return back()->with('error', 'CRITICAL_ERROR: ' . $e->getMessage());
        }
    }

    public function quickUpdate(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->update([
            'id_asisten' => $request->id_asisten ?: null
        ]);
        return back()->with('success', 'Asisten diperbarui!');
    }

    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        $hariLama = $schedule->hari;
        $matkulLama = $schedule->matkul;
        $tanggalLama = $schedule->tanggal;

        $id_lab = $request->id_lab ?? $schedule->id_lab;
        if ($request->lab) {
            $labObj = Lab::firstOrCreate(['nama_lab' => strtoupper($request->lab)], ['kapasitas' => 40, 'fasilitas' => '-']);
            $id_lab = $labObj->id_lab;
        }

        $id_asisten = $request->id_asisten ?? $schedule->id_asisten;
        if ($request->has('id_asisten') && empty($request->id_asisten)) {
            $id_asisten = null;
        }

        if ($request->nama_asisten) {
            if (strtoupper($request->nama_asisten) === 'TBD' || empty($request->nama_asisten)) {
                $id_asisten = null;
            } else {
                $asistenObj = AssistantSchedule::firstOrCreate(['nama_asisten' => $request->nama_asisten], ['hari' => '-', 'jam_mulai' => '00:00', 'jam_selesai' => '00:00', 'mata_kuliah' => '-']);
                $id_asisten = $asistenObj->id_asisten;
            }
        }

        $schedule->update([
            'tanggal'      => $request->tanggal ?? $schedule->tanggal,
            'hari'         => $request->tanggal ? \Carbon\Carbon::parse($request->tanggal)->locale('id')->translatedFormat('l') : $schedule->hari,
            'id_lab'       => $id_lab,
            'id_asisten'   => $id_asisten,
            'jam_mulai'    => $request->jam_mulai ?? $schedule->jam_mulai,
            'jam_selesai'  => $request->jam_selesai ?? $schedule->jam_selesai,
            'matkul'       => $request->matkul ?? $schedule->matkul,
            'sks'          => $request->sks ?? $schedule->sks,
            'dosen'        => $request->dosen ?? $schedule->dosen,
        ]);

        if ($request->update_scope === 'all') {
            Schedule::where('hari', $hariLama)
                ->where('matkul', $matkulLama)
                ->where('tanggal', '>', $tanggalLama) 
                ->update([
                    'id_lab'       => $id_lab,
                    'id_asisten'   => $id_asisten,
                    'jam_mulai'    => $request->jam_mulai ?? $schedule->jam_mulai,
                    'jam_selesai'  => $request->jam_selesai ?? $schedule->jam_selesai,
                    'dosen'        => $request->dosen ?? $schedule->dosen,
                ]);

            return back()->with('success', 'Perubahan disimpan permanen untuk semua minggu ke depan.');
        }

        return back()->with('success', 'Sukses! Perubahan tersimpan khusus hari ini saja.');
    } 

    public function clearSchedule()
    {
        Schedule::truncate();
        return back()->with('success', 'Wusss! Semua jadwal tetap berhasil disapu bersih.');
    }

   

    

   

 

   
}