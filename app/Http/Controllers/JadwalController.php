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
                             $query->where('nama_lab', '!=', 'RUANG ASISTEN')
                                   ->where('nama_lab', '!=', 'RA');
                         })
                         ->orderBy('jam_mulai', 'asc')
                         ->get();

        $labs = Lab::all();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json($schedules);
        }

        return view('welcome', compact('schedules', 'filterDate', 'labs'));
    }

        public function dashboard(Request $request) 
{
    $filterDate = $request->query('filter_date', now()->toDateString());
    $search = $request->query('search');
    $perPage = $request->query('per_page', 5); 

    $daySchedules = Schedule::with(['lab', 'assistantSchedule'])
        ->whereDate('tanggal', $filterDate)
        ->get();

    $query = Schedule::with(['lab', 'assistantSchedule'])
        ->whereDate('tanggal', $filterDate);

    if (!empty($search)) {
        $query->where(function($q) use ($search) {
            $q->where('matkul', 'like', "%{$search}%")
              ->orWhere('dosen', 'like', "%{$search}%")
              ->orWhereHas('lab', function($l) use ($search) {
                  $l->where('nama_lab', 'like', "%{$search}%");
              });
        });
    }

    $schedules = $query->orderBy('jam_mulai', 'asc')->paginate($perPage);
    $labs = Lab::all();

    return view('spv.dashboard', compact('schedules', 'labs', 'filterDate', 'daySchedules'));
}

    public function manajemenJadwal(Request $request) {
    $filterDate = $request->query('filter_date', now()->toDateString());
    $schedules = Schedule::with(['lab', 'assistantSchedule'])->whereDate('tanggal', $filterDate)->orderBy('jam_mulai', 'asc')->get();
    $labs = Lab::all();

    $checkData = Schedule::with('lab')
        ->whereHas('lab', function($query) {
            $query->where('nama_lab', '!=', 'RUANG RA');
        })
        ->get();

    $badIds = [];
    $grouped = $checkData->groupBy(function($item) {
        return $item->tanggal . '_' . $item->id_lab;
    });

    foreach ($grouped as $items) {
        if ($items->count() <= 1) {
            continue;
        }
        foreach ($items as $s1) {
            foreach ($items as $s2) {
                if ($s1->id_jadwal !== $s2->id_jadwal) {
                    if ($s1->jam_mulai < $s2->jam_selesai && $s1->jam_selesai > $s2->jam_mulai) {
                        $badIds[] = $s1->id_jadwal;
                    }
                }
            }
        }
    }

    $conflicts = $checkData->whereIn('id_jadwal', $badIds);

    return view('spv.jadwal', compact('schedules', 'labs','filterDate','conflicts'));
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
    $request->validate([
        'tanggal'      => 'required|date',
        'id_lab'       => 'required',
        'jam_mulai'    => 'required',
        'jam_selesai'  => 'required',
        'matkul'       => 'required|string',
        'dosen'        => 'required|string',
        'id_asisten'   => 'nullable',
        'update_scope' => 'required|string'
    ]);

    $schedule = Schedule::findOrFail($id);

    if ($request->input('update_scope') === 'all') {
        Schedule::where('id_lab', $schedule->id_lab)
            ->where('hari', $schedule->hari)
            ->where('jam_mulai', $schedule->jam_mulai)
            ->where('matkul', $schedule->matkul)
            ->where('tanggal', '>=', $schedule->tanggal)
            ->update([
                'id_lab'      => $request->id_lab,
                'jam_mulai'   => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'matkul'      => $request->matkul,
                'dosen'       => $request->dosen,
                'id_asisten'  => $request->id_asisten,
            ]);
    } else {
        $schedule->update([
            'tanggal'     => $request->tanggal,
            'id_lab'      => $request->id_lab,
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'matkul'      => $request->matkul,
            'dosen'       => $request->dosen,
            'id_asisten'  => $request->id_asisten,
        ]);
    }

    return back()->with('success', 'Perubahan jadwal berhasil diterapkan.');
}

    public function clearSchedule()
    {
        Schedule::truncate();
        return back()->with('success', 'Wusss! Semua jadwal tetap berhasil disapu bersih.');
    }

   public function importExcel(Request $request) 
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date',
            'file_excel' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        $sheets = Excel::toArray([], $request->file('file_excel'));
        $count = 0;

        foreach ($sheets as $rows) {
            foreach ($rows as $row) {
                if (empty(array_filter($row))) continue; 
                if (isset($row[1]) && str_contains(strtolower($row[1]), 'mata kuliah')) continue;

                $matkulRaw = $row[1] ?? '';
                $sks       = $row[3] ?? 0;
                $kelp      = $row[4] ?? '';
                $hariExcel = trim($row[5] ?? '');
                $jamStr    = $row[7] ?? '';
                $ruangRaw  = $row[8] ?? '';
                $dosen     = $row[9] ?? '';
                $asisten   = isset($row[10]) ? trim($row[10]) : '';

                if (str_contains(strtoupper($ruangRaw), 'LAB')) {
                    preg_match('/LAB\s?\d+/i', strtoupper($ruangRaw), $matches);
                    $namaLab = $matches[0] ?? 'LAB TBD';

                    $jamSplit = explode('-', $jamStr);
                    $jamMulai = trim($jamSplit[0]);
                    $jamSelesai = isset($jamSplit[1]) ? trim($jamSplit[1]) : '00:00';

                    $labObj = Lab::firstOrCreate(['nama_lab' => strtoupper($namaLab)], ['kapasitas' => 40, 'fasilitas' => '-']);
                    
                    $id_asisten = null;
                    if (!empty($asisten) && strtoupper($asisten) !== 'TBD') {
                        $asistenObj = AssistantSchedule::firstOrCreate(
                            ['nama_asisten' => $asisten], 
                            ['hari' => '-', 'jam_mulai' => '00:00', 'jam_selesai' => '00:00', 'mata_kuliah' => '-']
                        );
                        $id_asisten = $asistenObj->id_asisten;
                    }

                    $period = CarbonPeriod::create($request->start_date, $request->end_date);
                    
                    foreach ($period as $date) {
                        if (strtolower($date->locale('id')->translatedFormat('l')) == strtolower($hariExcel)) {
                            Schedule::create([
                                'tanggal'     => $date->format('Y-m-d'),
                                'hari'        => $hariExcel,
                                'id_lab'      => $labObj->id_lab,
                                'id_asisten'  => $id_asisten, 
                                'jam_mulai'   => $jamMulai,
                                'jam_selesai' => $jamSelesai,
                                'matkul'      => strtoupper($matkulRaw) . " ($kelp)",
                                'sks'         => $sks,
                                'dosen'       => $dosen,
                            ]);
                            $count++;
                        }
                    }
                }
            }
        }

        if ($count === 0) {
            return back()->with('error', 'Waduh, datanya kebaca tapi nggak nemu satupun jadwal yang ruangannya LAB.');
        }

        return back()->with('success', "🚀 Mantap! $count baris jadwal khusus LAB berhasil di-generate otomatis.");
    }

    public function index(Request $request)
{
    // ... (kodingan penarikan data $schedules dan $labs milik lu sebelumnya) ...

    // 🔥 LOGIKA CERDAS: Mencari semua jadwal yang tanggal & lab-nya kembar, tapi jam-nya bertubrukan
    $conflicts = Schedule::whereIn('id_jadwal', function($query) {
        $query->select('s1.id_jadwal')
            ->from('schedules as s1')
            ->join('schedules as s2', function($join) {
                $join->on('s1.tanggal', '=', 's2.tanggal')
                     ->on('s1.id_lab', '=', 's2.id_lab')
                     ->on('s1.id_jadwal', '!=', 's2.id_jadwal') // Gak boleh mendeteksi dirinya sendiri
                     ->whereRaw('s1.jam_mulai < s2.jam_selesai') // Waktu mulai kelas A sebelum kelas B selesai
                     ->whereRaw('s1.jam_selesai > s2.jam_mulai'); // Waktu selesai kelas A melewati kelas B mulai
            });
    })->with('lab')->get(); // Load relasi lab-nya sekalian

    // Lempar data variabel $conflicts ke view blade
    return view('spv.jadwal', compact('schedules', 'labs', 'conflicts'));
}

    

   

 

   
}