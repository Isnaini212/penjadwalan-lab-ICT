<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\AssistantSchedule;
use App\Models\Lab;
use App\Models\Dosen;
use App\Models\Ormawa;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;


class JadwalController extends Controller
{

public function minggu(){
    // Cari tanggal paling awal dan paling akhir KHUSUS untuk praktikum (kecuali RA)
    $minDate = Schedule::whereHas('lab', function ($query) {
        $query->whereNotIn('nama_lab', ['RUANG RA', 'RA', 'RUANG ASISTEN']);
    })->min('tanggal');

    $maxDate = Schedule::whereHas('lab', function ($query) {
        $query->whereNotIn('nama_lab', ['RUANG RA', 'RA', 'RUANG ASISTEN']);
    })->max('tanggal');

    if (!$minDate || !$maxDate) {
        $startPeriode = Carbon::now()->startOfWeek(Carbon::MONDAY);
        $endPeriode   = Carbon::now()->endOfWeek(Carbon::SUNDAY);
    } else {
        $startPeriode = Carbon::parse($minDate)->startOfWeek(Carbon::MONDAY);
        $endPeriode   = Carbon::parse($maxDate)->endOfWeek(Carbon::SUNDAY);
    }

        $listMinggu  = [];
        $current     = $startPeriode->copy();
        $index       = 1;
        $today       = Carbon::now()->toDateString();
        $defaultWeek = 1;

        while ($current->lessThanOrEqualTo($endPeriode)) {
            $senin  = $current->copy()->startOfWeek(Carbon::MONDAY)->toDateString();
            $minggu = $current->copy()->endOfWeek(Carbon::SUNDAY)->toDateString();

            $listMinggu[] = [
                'id_minggu' => $index,
                'label'     => 'Minggu ' . $index,
                'start'     => $senin,
                'end'       => $minggu,
            ];

            if ($today >= $senin && $today <= $minggu) {
                $defaultWeek = $index;
            }

            $current->addWeek();
            $index++;
        }

        return view('mingguan.perminggu', compact('listMinggu', 'defaultWeek'));
}

public function cetakMinggu(Request $request)
    {
        $request->validate([
            'week' => 'required|integer'
        ]);

        $minDate = Schedule::min('tanggal');
        $maxDate = Schedule::max('tanggal');

        if (!$minDate || !$maxDate) {
            return back()->with('error', 'Jadwal kosong, tidak ada yang bisa dicetak.');
        }

        $startPeriode = Carbon::parse($minDate)->startOfWeek(Carbon::MONDAY);
        $endPeriode   = Carbon::parse($maxDate)->endOfWeek(Carbon::SUNDAY);

        $listMinggu  = [];
        $current     = $startPeriode->copy();
        $index       = 1;

        while ($current->lessThanOrEqualTo($endPeriode)) {
            $listMinggu[$index] = [
                'label' => 'Minggu ' . $index,
                'start' => $current->copy()->startOfWeek(Carbon::MONDAY)->toDateString(),
                'end'   => $current->copy()->endOfWeek(Carbon::SUNDAY)->toDateString(),
            ];
            $current->addWeek();
            $index++;
        }

        $mingguDipilih = (int) $request->query('week');
        $activeRange   = $listMinggu[$mingguDipilih] ?? null;

        if (!$activeRange) {
            return abort(404, 'Minggu perkuliahan tidak ditemukan.');
        }

        // Ambil data murni langsung dari database berdasarkan parameter minggu terpilih
        $schedules = Schedule::with(['lab', 'assistantSchedule'])
            ->whereDate('tanggal', '>=', $activeRange['start'])
            ->whereDate('tanggal', '<=', $activeRange['end'])
            ->whereHas('lab', function ($query) {
                $query->whereNotIn('nama_lab', ['RUANG RA', 'RA','RUANG ASISTEN']);
            })
            ->orderBy('tanggal', 'asc')
            ->orderBy('jam_mulai', 'asc')
            ->get();

        $namaFile = 'Jadwal_Lab_ICT_Minggu_' . $mingguDipilih . '.pdf';

        // Lempar data ke file view khusus layout PDF cetak
        $pdf = Pdf::loadView('mingguan.cetak', [
            'schedules'   => $schedules,
            'activeRange' => $activeRange,
            'minggu'      => $mingguDipilih
        ])->setPaper('a4', 'landscape'); // Kita set landscape biar tabel rapi kesamping

        return $pdf->stream($namaFile); // .stream() agar PDF terbuka di tab baru, ganti .download() jika ingin langsung terunduh
}


public function welcome(Request $request)

    {


        $filterDate = $request->query('filter_date', now()->toDateString());
        $labs = Lab::all();
        $schedules = Schedule::with(['lab', 'assistantSchedule'])
                         ->whereDate('tanggal', $filterDate)
                         ->whereHas('lab', function($query) {
                             $query->where('nama_lab', '!=', 'RUANG ASISTEN')
                                   ->where('nama_lab', '!=', 'RA')
                                   ->where('nama_lab', '!=', 'RUANG RA');
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
            'tanggal'         => 'required|date',
            'repeat_type'     => 'required|in:single,daily,weekdays,weekly',
            'tanggal_selesai' => 'nullable|required_unless:repeat_type,single|date|after_or_equal:tanggal',
            'id_lab'          => 'nullable',
            'lab'             => 'nullable',
            'jam_mulai'       => 'required',
            'matkul'          => 'required',
            'sks'             => 'required|numeric',
            'dosen'           => 'required',
        ]);

        $menit = $request->sks * 50;
        $jam_selesai = date('H:i', strtotime($request->jam_mulai . " + $menit minutes"));

        $id_lab = $request->id_lab;
        if (!$id_lab && $request->lab) {
            $labObj = Lab::firstOrCreate(['nama_lab' => strtoupper($request->lab)], ['kapasitas' => 40, 'fasilitas' => '-']);
            $id_lab = $labObj->id_lab;
        }

        $id_asisten = $request->id_asisten ?: null;

        // Tentukan tanggal-tanggal yang akan diinsert
        $dates = [];
        if ($request->repeat_type === 'single') {
            $dates[] = Carbon::parse($request->tanggal)->format('Y-m-d');
        } else if ($request->tanggal_selesai) {
            $period = CarbonPeriod::create($request->tanggal, $request->tanggal_selesai);
            
            if ($request->repeat_type === 'weekly') {
                $startDayOfWeek = Carbon::parse($request->tanggal)->dayOfWeek;
                foreach ($period as $date) {
                    if ($date->dayOfWeek === $startDayOfWeek) {
                        $dates[] = $date->format('Y-m-d');
                    }
                }
            } elseif ($request->repeat_type === 'daily') {
                foreach ($period as $date) {
                    $dates[] = $date->format('Y-m-d');
                }
            } elseif ($request->repeat_type === 'weekdays') {
                foreach ($period as $date) {
                    if ($date->isWeekday()) {
                        $dates[] = $date->format('Y-m-d');
                    }
                }
            }
        } else {
            $dates[] = Carbon::parse($request->tanggal)->format('Y-m-d');
        }

        // Cek konflik untuk seluruh tanggal
        $conflicts = [];
        if ($id_lab) {
            foreach ($dates as $tgl) {
                // 1. Cek schedule
                $conflict = Schedule::where('tanggal', $tgl)
                    ->where('id_lab', $id_lab)
                    ->where(function ($query) use ($request, $jam_selesai) {
                        $query->where('jam_mulai', '<', $jam_selesai)
                              ->where('jam_selesai', '>', $request->jam_mulai);
                    })->first();

                if ($conflict) {
                    $formattedDate = Carbon::parse($tgl)->translatedFormat('d M Y');
                    $conflicts[] = "Tanggal {$formattedDate} bentrok dengan matkul {$conflict->matkul} ({$conflict->jam_mulai} - {$conflict->jam_selesai})";
                }

                // 2. Cek approved Dosen booking
                $conflictDosen = Dosen::where('tanggal', $tgl)
                    ->where('id_lab', $id_lab)
                    ->where('status', 'approved')
                    ->where(function ($query) use ($request, $jam_selesai) {
                        $query->where('jam_mulai', '<', $jam_selesai)
                              ->where('jam_selesai', '>', $request->jam_mulai);
                    })->first();

                if ($conflictDosen) {
                    $formattedDate = Carbon::parse($tgl)->translatedFormat('d M Y');
                    $conflicts[] = "Tanggal {$formattedDate} bentrok dengan booking Dosen {$conflictDosen->nm_dosen} (Acara: {$conflictDosen->keperluan}) pada jam " . substr($conflictDosen->jam_mulai, 0, 5) . " - " . substr($conflictDosen->jam_selesai, 0, 5);
                }

                // 3. Cek approved Ormawa booking
                $conflictOrmawa = Ormawa::where('tanggal', $tgl)
                    ->where('lab', $id_lab)
                    ->where('status', 'approved')
                    ->where(function ($query) use ($request, $jam_selesai) {
                        $query->where('jam_mulai', '<', $jam_selesai)
                              ->where('jam_selesai', '>', $request->jam_mulai);
                    })->first();

                if ($conflictOrmawa) {
                    $formattedDate = Carbon::parse($tgl)->translatedFormat('d M Y');
                    $conflicts[] = "Tanggal {$formattedDate} bentrok dengan booking Ormawa {$conflictOrmawa->nama_ormawa} (Acara: {$conflictOrmawa->keperluan}) pada jam " . substr($conflictOrmawa->jam_mulai, 0, 5) . " - " . substr($conflictOrmawa->jam_selesai, 0, 5);
                }
            }
        }

        if (!empty($conflicts)) {
            return back()->withInput()->with('error', 'Gagal menambah jadwal karena terdeteksi bentrok pada: ' . implode(', ', $conflicts));
        }

        // Simpan semua jadwal jika tidak ada bentrok sama sekali
        $count = 0;
        foreach ($dates as $tgl) {
            $hari = Carbon::parse($tgl)->locale('id')->translatedFormat('l');
            Schedule::create([
                'tanggal'     => $tgl,
                'hari'        => $hari,
                'id_lab'      => $id_lab,
                'id_asisten'  => $id_asisten,
                'jam_mulai'   => $request->jam_mulai,
                'jam_selesai' => $jam_selesai,
                'matkul'      => $request->matkul,
                'sks'         => $request->sks,
                'dosen'       => $request->dosen,
            ]);
            $count++;
        }

        $pesan = $count > 1 ? "Berhasil menambahkan {$count} jadwal mingguan!" : 'Jadwal berhasil ditambahkan!';
        return back()->with('success', $pesan);
}

    public function checkLabsRange(Request $request)
    {
        $request->validate([
            'tanggal'         => 'required|date',
            'repeat_type'     => 'required|in:single,daily,weekdays,weekly',
            'tanggal_selesai' => 'nullable|required_unless:repeat_type,single|date|after_or_equal:tanggal',
            'jam_mulai'       => 'required',
            'sks'             => 'required|numeric',
        ]);

        $menit = $request->sks * 50;
        $jam_selesai = date('H:i', strtotime($request->jam_mulai . " + $menit minutes"));

        // Tentukan tanggal-tanggal yang akan dicheck
        $dates = [];
        if ($request->repeat_type === 'single') {
            $dates[] = Carbon::parse($request->tanggal)->format('Y-m-d');
        } else if ($request->tanggal_selesai) {
            $period = CarbonPeriod::create($request->tanggal, $request->tanggal_selesai);
            if ($request->repeat_type === 'weekly') {
                $startDayOfWeek = Carbon::parse($request->tanggal)->dayOfWeek;
                foreach ($period as $date) {
                    if ($date->dayOfWeek === $startDayOfWeek) {
                        $dates[] = $date->format('Y-m-d');
                    }
                }
            } elseif ($request->repeat_type === 'daily') {
                foreach ($period as $date) {
                    $dates[] = $date->format('Y-m-d');
                }
            } elseif ($request->repeat_type === 'weekdays') {
                foreach ($period as $date) {
                    if ($date->isWeekday()) {
                        $dates[] = $date->format('Y-m-d');
                    }
                }
            }
        } else {
            $dates[] = Carbon::parse($request->tanggal)->format('Y-m-d');
        }

        $allLabs = Lab::where('nama_lab', '!=', 'RUANG ASISTEN')->get();

        $response = [];
        foreach ($allLabs as $lab) {
            $conflicts = [];
            foreach ($dates as $tgl) {
                // Cek schedule
                $hasSchedule = Schedule::where('tanggal', $tgl)
                    ->where('id_lab', $lab->id_lab)
                    ->where(function($q) use ($request, $jam_selesai) {
                        $q->where('jam_mulai', '<', $jam_selesai)
                          ->where('jam_selesai', '>', $request->jam_mulai);
                    })->exists();

                // Cek approved Dosen booking
                $hasDosen = Dosen::where('tanggal', $tgl)
                    ->where('id_lab', $lab->id_lab)
                    ->where('status', 'approved')
                    ->where(function($q) use ($request, $jam_selesai) {
                        $q->where('jam_mulai', '<', $jam_selesai)
                          ->where('jam_selesai', '>', $request->jam_mulai);
                    })->exists();

                // Cek approved Ormawa booking
                $hasOrmawa = Ormawa::where('tanggal', $tgl)
                    ->where('lab', $lab->id_lab)
                    ->where('status', 'approved')
                    ->where(function($q) use ($request, $jam_selesai) {
                        $q->where('jam_mulai', '<', $jam_selesai)
                          ->where('jam_selesai', '>', $request->jam_mulai);
                    })->exists();

                if ($hasSchedule || $hasDosen || $hasOrmawa) {
                    $formattedDate = Carbon::parse($tgl)->translatedFormat('d M');
                    $conflicts[] = $formattedDate;
                }
            }

            $isBusy = !empty($conflicts);
            $response[] = [
                'id_lab'    => $lab->id_lab,
                'nama_lab'  => $lab->nama_lab,
                'is_busy'   => $isBusy,
                'conflict_dates' => $conflicts
            ];
        }

        return response()->json([
            'jam_selesai' => $jam_selesai,
            'labs'        => $response
        ]);
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
        'tanggal' => 'required|date',
        'matkul' => 'required',
        'dosen' => 'required',
        'id_lab' => 'required',
        'jam_mulai' => 'required',
        'jam_selesai' => 'required',
    ]);

    $schedule = Schedule::findOrFail($id);

    
    $matkulLama     = $schedule->matkul;
    $dosenLama      = $schedule->dosen;
    $jamMulaiLama   = $schedule->jam_mulai;
    $jamSelesaiLama = $schedule->jam_selesai;

    \Carbon\Carbon::setLocale('id');
    $namaHariOtomatis = \Carbon\Carbon::parse($request->tanggal)
        ->translatedFormat('l');

    
    if ($request->scope === 'all') {

        Schedule::where('matkul', $matkulLama)
            ->where('dosen', $dosenLama)
            ->where('jam_mulai', $jamMulaiLama)
            ->where('jam_selesai', $jamSelesaiLama)
            ->update([
                'matkul'      => $request->matkul,
                'dosen'       => $request->dosen,
                'jam_mulai'   => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'id_lab'      => $request->id_lab,
                'id_asisten'  => $request->id_asisten,
            ]);

    } else {

        
        $schedule->update([
            'matkul'      => $request->matkul,
            'dosen'       => $request->dosen,
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'id_lab'      => $request->id_lab,
            'id_asisten'  => $request->id_asisten,
            'tanggal'     => $request->tanggal,
            'hari'        => $namaHariOtomatis,
        ]);
    }

    return redirect()->back()
        ->with('success', 'Jadwal berhasil diperbarui!');
}
    public function bersihin()
    {
        Schedule::truncate();
        return back()->with('success', 'Wusss! Semua jadwal tetap berhasil disapu bersih.');
    }

     public function importExcel(Request $request) 
    {
        $request->validate([
            'start_date' => 'required|date',
            'end_date'   => 'required|date',
            'file_excel' => 'required|mimes:xlsx,xls,csv,txt|max:5120'
        ], [
            'file_excel.required' => 'File Excel/CSV belum dipilih.',
            'file_excel.mimes'    => 'Format file ditolak oleh server! Harap pastikan file Anda benar-benar berformat Excel (.xlsx, .xls) atau CSV (.csv).',
            'file_excel.max'      => 'Ukuran file jadwal terlalu besar (maksimal 5 MB).',
            'start_date.required' => 'Tanggal periode mulai wajib diisi.',
            'end_date.required'   => 'Tanggal periode selesai wajib diisi.',
        ]);

        $sheets = Excel::toArray([], $request->file('file_excel'));
        $count = 0;
        $bentrokCount = 0;

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
    
                    // Hanya ambil angka jika ada kata "LAB" langsung diikuti angka (boleh pakai spasi)
                    if (preg_match('/LAB\s*(\d+)/i', $ruangRaw, $matches)) {
                        $angkaLab = $matches[1]; 
                    } else {
                        continue; // Jika formatnya bukan "LAB [angka]" (seperti LAB SK 2), lewati baris ini
                    }

                    // Format angka agar menjadi 2 digit sesuai Seeder (contoh: "2" menjadi "02")
                    $angkaFormat = str_pad($angkaLab, 2, '0', STR_PAD_LEFT);
                    $namaLabDicari = "Lab " . $angkaFormat; 

                    // Cari ke database, pastikan Lab tersebut terdaftar di Seeder
                    $labObj = Lab::where('nama_lab', $namaLabDicari)->first();

                    // JIKA TIDAK ADA DI DATABASE (SEEDER), JANGAN IMPORT & SKIP BARIS INI
                    if (!$labObj) {
                        continue; 
                    }

                    // Pecah jam kuliah (Sama seperti kode lama kamu)
                    $jamSplit = explode('-', $jamStr);
                    $jamMulai = trim($jamSplit[0]);
                    $jamSelesai = isset($jamSplit[1]) ? trim($jamSplit[1]) : '00:00';
    
                    
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
                            $tanggalFormat = $date->format('Y-m-d');
                            
                            $conflict = Schedule::where('tanggal', $tanggalFormat)
                                ->where('id_lab', $labObj->id_lab)
                                ->where(function ($query) use ($jamMulai, $jamSelesai) {
                                    $query->where('jam_mulai', '<', $jamSelesai)
                                          ->where('jam_selesai', '>', $jamMulai);
                                })->first();

                            if ($conflict) {
                                $bentrokCount++;
                                continue;
                            }

                            Schedule::create([
                                'tanggal'     => $tanggalFormat,
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

        if ($count === 0 && $bentrokCount > 0) {
            return back()->with('error', "Gagal import! $bentrokCount jadwal terdeteksi bentrok dengan jadwal lain dan tidak dimasukkan ke database.");
        }

        if ($count === 0) {
            return back()->with('error', 'Waduh, datanya kebaca tapi nggak nemu satupun jadwal yang ruangannya LAB.');
        }

        $pesan = "Mantap! $count baris jadwal khusus LAB berhasil di-generate otomatis.";
        if ($bentrokCount > 0) {
            $pesan .= " Namun ada $bentrokCount jadwal yang dilewati (tidak masuk db) karena bentrok waktu dan ruangan.";
        }

        return back()->with('success', $pesan);
    }
















    }