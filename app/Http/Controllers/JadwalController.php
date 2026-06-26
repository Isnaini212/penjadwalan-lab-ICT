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
        $schedules = Schedule::with(['lab', 'assistants'])
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
        $schedules = Schedule::with(['lab', 'assistants'])
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

    $daySchedules = Schedule::with(['lab', 'assistants'])
        ->whereDate('tanggal', $filterDate)
        ->get();

    $query = Schedule::with(['lab', 'assistants'])
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
    $schedules = Schedule::with(['lab', 'assistants'])->whereDate('tanggal', $filterDate)->orderBy('jam_mulai', 'asc')->get();
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

        $dayOfWeek = Carbon::parse($request->tanggal)->dayOfWeek;
        if ($dayOfWeek === Carbon::SUNDAY && $request->repeat_type === 'single') {
            return back()->withInput()->with('error', 'Gagal! Hari Minggu adalah hari libur, tidak bisa melakukan penjadwalan.');
        }

        $jam_mulai_formatted = Carbon::parse($request->jam_mulai)->format('H:i');
        if ($dayOfWeek === Carbon::SATURDAY) {
            $allowedSaturday = ['08:00', '10:00', '13:00', '15:00'];
            if (!in_array($jam_mulai_formatted, $allowedSaturday)) {
                return back()->withInput()->with('error', 'Gagal! Pada hari Sabtu, jam mulai harus salah satu dari: 08:00, 10:00, 13:00, 15:00.');
            }
        } else if ($dayOfWeek !== Carbon::SUNDAY) {
            $allowedWeekday = ['07:10', '08:00', '08:55', '09:45', '10:40', '11:35', '12:30', '13:25', '14:20', '15:15', '16:10', '17:05', '18:00', '18:55'];
            if (!in_array($jam_mulai_formatted, $allowedWeekday)) {
                return back()->withInput()->with('error', 'Gagal! Jam mulai tidak valid untuk hari kerja.');
            }

            // Validasi SKS untuk weekdays
            $weekdayStarts = ['07:10', '08:00', '08:55', '09:45', '10:40', '11:35', '12:30', '13:25', '14:20', '15:15', '16:10', '17:05', '18:00', '18:55', '19:50'];
            $startIndex = array_search($jam_mulai_formatted, $weekdayStarts);
            if ($startIndex !== false) {
                $maxSks = min(4, 15 - $startIndex);
                if ((int)$request->sks > $maxSks) {
                    return back()->withInput()->with('error', "Gagal! Untuk jam mulai {$jam_mulai_formatted}, SKS maksimal yang diperbolehkan adalah {$maxSks} agar waktu selesai tidak melewati 20:40.");
                }
            } else {
                if ((int)$request->sks < 1 || (int)$request->sks > 4) {
                    return back()->withInput()->with('error', 'Gagal! SKS harus bernilai antara 1 sampai 4.');
                }
            }
        }

        $jam_selesai = $this->calculateEndTime($request->tanggal, $request->jam_mulai, $request->sks);

        $id_lab = $request->id_lab;
        if (!$id_lab && $request->lab) {
            $labObj = Lab::firstOrCreate(['nama_lab' => strtoupper($request->lab)], ['kapasitas' => 40, 'fasilitas' => '-']);
            $id_lab = $labObj->id_lab;
        }

        // Ambil array id_asisten (mendukung multi-asisten)
        $assistantIds = array_filter((array) ($request->id_asisten ?? []));


        // Tentukan tanggal-tanggal yang akan diinsert
        $dates = [];
        if ($request->repeat_type === 'single') {
            $dates[] = Carbon::parse($request->tanggal)->format('Y-m-d');
        } else if ($request->tanggal_selesai) {
            $period = CarbonPeriod::create($request->tanggal, $request->tanggal_selesai);
            
            if ($request->repeat_type === 'weekly') {
                $startDayOfWeek = Carbon::parse($request->tanggal)->dayOfWeek;
                foreach ($period as $date) {
                    if ($date->dayOfWeek === $startDayOfWeek && $date->dayOfWeek !== Carbon::SUNDAY) {
                        $dates[] = $date->format('Y-m-d');
                    }
                }
            } elseif ($request->repeat_type === 'daily') {
                foreach ($period as $date) {
                    if ($date->dayOfWeek !== Carbon::SUNDAY) {
                        $dates[] = $date->format('Y-m-d');
                    }
                }
            } elseif ($request->repeat_type === 'weekdays') {
                foreach ($period as $date) {
                    if ($date->isWeekday() && $date->dayOfWeek !== Carbon::SUNDAY) {
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
            $schedule = Schedule::create([
                'tanggal'     => $tgl,
                'hari'        => $hari,
                'id_lab'      => $id_lab,
                'jam_mulai'   => $request->jam_mulai,
                'jam_selesai' => $jam_selesai,
                'matkul'      => $request->matkul,
                'sks'         => $request->sks,
                'dosen'       => $request->dosen,
            ]);

            // Sync asisten via pivot table (mendukung multi-asisten)
            if (!empty($assistantIds)) {
                $schedule->assistants()->sync($assistantIds);
            }

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

        $jam_selesai = $this->calculateEndTime($request->tanggal, $request->jam_mulai, $request->sks);

        // Tentukan tanggal-tanggal yang akan dicheck
        $dates = [];
        if ($request->repeat_type === 'single') {
            if (Carbon::parse($request->tanggal)->dayOfWeek !== Carbon::SUNDAY) {
                $dates[] = Carbon::parse($request->tanggal)->format('Y-m-d');
            }
        } else if ($request->tanggal_selesai) {
            $period = CarbonPeriod::create($request->tanggal, $request->tanggal_selesai);
            if ($request->repeat_type === 'weekly') {
                $startDayOfWeek = Carbon::parse($request->tanggal)->dayOfWeek;
                foreach ($period as $date) {
                    if ($date->dayOfWeek === $startDayOfWeek && $date->dayOfWeek !== Carbon::SUNDAY) {
                        $dates[] = $date->format('Y-m-d');
                    }
                }
            } elseif ($request->repeat_type === 'daily') {
                foreach ($period as $date) {
                    if ($date->dayOfWeek !== Carbon::SUNDAY) {
                        $dates[] = $date->format('Y-m-d');
                    }
                }
            } elseif ($request->repeat_type === 'weekdays') {
                foreach ($period as $date) {
                    if ($date->isWeekday() && $date->dayOfWeek !== Carbon::SUNDAY) {
                        $dates[] = $date->format('Y-m-d');
                    }
                }
            }
        } else {
            if (Carbon::parse($request->tanggal)->dayOfWeek !== Carbon::SUNDAY) {
                $dates[] = Carbon::parse($request->tanggal)->format('Y-m-d');
            }
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
        
        // Sync asisten via pivot table (mendukung multi-asisten)
        $assistantIds = array_filter((array) ($request->id_asisten ?? []));
        $schedule->assistants()->sync($assistantIds);
        
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

    $dayOfWeek = \Carbon\Carbon::parse($request->tanggal)->dayOfWeek;
    $jam_mulai_formatted = \Carbon\Carbon::parse($request->jam_mulai)->format('H:i');
    $jam_selesai_formatted = \Carbon\Carbon::parse($request->jam_selesai)->format('H:i');

    if ($dayOfWeek === \Carbon\Carbon::SUNDAY) {
        return back()->withInput()->with('error', 'Gagal! Hari Minggu adalah hari libur, tidak bisa melakukan penjadwalan.');
    }

    if ($jam_mulai_formatted >= $jam_selesai_formatted) {
        return back()->withInput()->with('error', 'Gagal! Jam mulai harus lebih awal daripada jam selesai.');
    }

    if ($dayOfWeek === \Carbon\Carbon::SATURDAY) {
        if ($jam_mulai_formatted < '08:00' || $jam_selesai_formatted > '18:30') {
            return back()->withInput()->with('error', 'Gagal! Untuk hari Sabtu, jadwal harus berada antara pukul 08:00 s/d 18:30.');
        }
    } else {
        if ($jam_mulai_formatted < '07:10' || $jam_selesai_formatted > '20:40') {
            return back()->withInput()->with('error', 'Gagal! Untuk hari kerja, jadwal harus berada antara pukul 07:10 s/d 20:40.');
        }
    }

    $schedule = Schedule::findOrFail($id);

    
    $matkulLama     = $schedule->matkul;
    $dosenLama      = $schedule->dosen;
    $jamMulaiLama   = $schedule->jam_mulai;
    $jamSelesaiLama = $schedule->jam_selesai;

    \Carbon\Carbon::setLocale('id');
    $namaHariOtomatis = \Carbon\Carbon::parse($request->tanggal)
        ->translatedFormat('l');

    
    // Ambil array id_asisten (mendukung multi-asisten)
    $assistantIds = array_filter((array) ($request->id_asisten ?? []));

    if ($request->scope === 'all') {

        $affectedSchedules = Schedule::where('matkul', $matkulLama)
            ->where('dosen', $dosenLama)
            ->where('jam_mulai', $jamMulaiLama)
            ->where('jam_selesai', $jamSelesaiLama)
            ->get();

        foreach ($affectedSchedules as $affectedSchedule) {
            $affectedSchedule->update([
                'matkul'      => $request->matkul,
                'dosen'       => $request->dosen,
                'jam_mulai'   => $request->jam_mulai,
                'jam_selesai' => $request->jam_selesai,
                'id_lab'      => $request->id_lab,
            ]);
            $affectedSchedule->assistants()->sync($assistantIds);
        }

    } else {

        $schedule->update([
            'matkul'      => $request->matkul,
            'dosen'       => $request->dosen,
            'jam_mulai'   => $request->jam_mulai,
            'jam_selesai' => $request->jam_selesai,
            'id_lab'      => $request->id_lab,
            'tanggal'     => $request->tanggal,
            'hari'        => $namaHariOtomatis,
        ]);
        $schedule->assistants()->sync($assistantIds);
    }

    return redirect()->back()
        ->with('success', 'Jadwal berhasil diperbarui!');
}
    public function bersihin()
    {
        \Illuminate\Support\Facades\DB::table('schedule_assistant')->delete();
        Schedule::query()->delete();
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
    
                    
                    $importAssistantId = null;
                    if (!empty($asisten) && strtoupper($asisten) !== 'TBD') {
                        $asistenObj = AssistantSchedule::firstOrCreate(
                            ['nama_asisten' => $asisten], 
                            ['hari' => '-', 'jam_mulai' => '00:00', 'jam_selesai' => '00:00', 'mata_kuliah' => '-']
                        );
                        $importAssistantId = $asistenObj->id_asisten;
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

                            $newSchedule = Schedule::create([
                                'tanggal'     => $tanggalFormat,
                                'hari'        => $hariExcel,
                                'id_lab'      => $labObj->id_lab,
                                'jam_mulai'   => $jamMulai,
                                'jam_selesai' => $jamSelesai,
                                'matkul'      => strtoupper($matkulRaw) . " ($kelp)",
                                'sks'         => $sks,
                                'dosen'       => $dosen,
                            ]);

                            // Attach asisten via pivot table
                            if ($importAssistantId) {
                                $newSchedule->assistants()->attach($importAssistantId);
                            }
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
















    
private function calculateEndTime($tanggal, $jam_mulai, $sks)
{
    $dayOfWeek = Carbon::parse($tanggal)->dayOfWeek;
    $sks = (int) $sks;

    if ($dayOfWeek === Carbon::SATURDAY) {
        $saturdayEnds = [
            '08:00' => ['08:50', '09:50', '10:40', '11:30'],
            '10:00' => ['10:50', '11:50', '12:40', '13:30'],
            '13:00' => ['13:50', '14:50', '15:40', '16:30'],
            '15:00' => ['15:50', '16:50', '17:40', '18:30'],
        ];
        
        $jam_mulai_formatted = Carbon::parse($jam_mulai)->format('H:i');
        if (isset($saturdayEnds[$jam_mulai_formatted][$sks - 1])) {
            return $saturdayEnds[$jam_mulai_formatted][$sks - 1];
        }
        
        $minutes = $sks * 50;
        return Carbon::parse($jam_mulai)->addMinutes($minutes)->format('H:i');
    } else {
        $jam_mulai_formatted = Carbon::parse($jam_mulai)->format('H:i');
        
        $weekdayStarts = ['07:10', '08:00', '08:55', '09:45', '10:40', '11:35', '12:30', '13:25', '14:20', '15:15', '16:10', '17:05', '18:00', '18:55', '19:50'];
        $weekdayEnds   = ['08:00', '08:50', '09:40', '10:35', '11:30', '12:25', '13:20', '14:15', '15:10', '16:05', '17:00', '17:55', '18:50', '19:45', '20:40'];
        
        $startIndex = array_search($jam_mulai_formatted, $weekdayStarts);
        if ($startIndex !== false) {
            $endIndex = $startIndex + $sks - 1;
            if ($endIndex < count($weekdayEnds)) {
                return $weekdayEnds[$endIndex];
            } else {
                return end($weekdayEnds);
            }
        }
        
        $minutes = $sks * 50;
        return Carbon::parse($jam_mulai)->addMinutes($minutes)->format('H:i');
    }
}
}