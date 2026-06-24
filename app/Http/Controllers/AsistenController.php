<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AssistantSchedule;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Lab;
use App\Models\Schedule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AsistenController extends Controller
{

    public function jadwalAsisten(Request $request)
    {
        $semuaAsisten = AssistantSchedule::select('nama_asisten')->distinct()->get();

        $namaDicari = $request->query('nama');
        $hariDicari = $request->query('hari');

        if ($namaDicari || $hariDicari) {
            $query = AssistantSchedule::query();

            if ($namaDicari) { $query->where('nama_asisten', $namaDicari); }
            if ($hariDicari) { $query->where('hari', $hariDicari); }

            $asistenSchedules = $query->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
                                      ->orderBy('jam_mulai', 'asc')
                                      ->get();
        } else {
            $asistenSchedules = collect([]);
        }

        return view('spv.asisten', compact('semuaAsisten', 'asistenSchedules', 'namaDicari', 'hariDicari'));
        }



    public function storeAsisten(Request $request)
    {
        $request->validate([
            'nama_asisten' => 'required|string',
            'hari'         => 'required|string',
            'jam_mulai'    => 'required',
            'sks'          => 'required|numeric',
            'mata_kuliah'  => 'required|string',
        ]);

        $menit = $request->sks * 50;
        $jam_selesai = date('H:i', strtotime($request->jam_mulai . " + $menit minutes"));

        AssistantSchedule::create([
            'nama_asisten' => $request->nama_asisten,
            'hari'         => $request->hari,
            'jam_mulai'    => $request->jam_mulai,
            'jam_selesai'  => $jam_selesai,
            'mata_kuliah'  => $request->mata_kuliah,
        ]);

        return back()->with('success', 'Jadwal mata kuliah baru berhasil ditambahkan!');
    }



    public function updateAsisten(Request $request, $id)
    {

    $request->validate([
        'nama_asisten' => 'required|string|max:255',
        'hari'         => 'required|string',
        'jam_mulai'    => 'required',
        'jam_selesai'  => 'required',
        'mata_kuliah'  => 'required|string|max:255',
    ]);


    $jadwal = AssistantSchedule::where('id_asisten', $id)->first();


    if (!$jadwal) {
        return back()->with('error', 'Gagal! Data jadwal asisten tidak ditemukan.');
    }


    $jadwal->nama_asisten = $request->nama_asisten;
    $jadwal->hari         = $request->hari;
    $jadwal->jam_mulai    = $request->jam_mulai;
    $jadwal->jam_selesai  = $request->jam_selesai;
    $jadwal->mata_kuliah  = $request->mata_kuliah;


    $jadwal->save();


    return back()->with('success', 'Jadwal asisten berhasil diperbarui secara permanen!');
    }

    public function destroyAsisten($id)
    {
        $asisten = AssistantSchedule::findOrFail($id);
        $asisten->delete();
        return back()->with('success', 'Jadwal asisten berhasil dihapus!');
    }
    public function importAsistenExcel(Request $request)
    {
        $request->validate([
            'file_asisten' => 'required|mimes:xlsx,xls,csv,txt|max:5120'
        ], [
            'file_asisten.required' => 'File Excel/CSV belum dipilih.',
            'file_asisten.mimes'    => 'Format file ditolak oleh server! Harap pastikan file Anda benar-benar berformat Excel (.xlsx, .xls) atau CSV (.csv).',
            'file_asisten.max'      => 'Ukuran file jadwal terlalu besar (maksimal 5 MB).'
        ]);

        $sheets = Excel::toArray([], $request->file('file_asisten'));
        $count = 0;

        foreach ($sheets as $rows) {
            $currentAssistant = 'TBD';
            $pendingSchedules = [];
            $validFormatFound = false;
            $hariMap = [];

            $flushSchedule = function($hari) use (&$pendingSchedules, &$count) {
                if (isset($pendingSchedules[$hari])) {
                    AssistantSchedule::create($pendingSchedules[$hari]);
                    $count++;
                    unset($pendingSchedules[$hari]);
                }
            };

            foreach ($rows as $row) {
                if (empty(array_filter($row))) continue;

                $seninIndex = array_search('Senin', $row);
                $selasaIndex = array_search('Selasa', $row);
                
                // Cek apakah baris ini adalah HEADER HARI (Senin, Selasa, dll)
                if ($seninIndex !== false && $selasaIndex !== false) {
                    $validFormatFound = true;
                    foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $h) {
                        $flushSchedule($h);
                    }
                    
                    $currentAssistant = !empty($row[0]) ? $row[0] : (!empty($row[1]) ? $row[1] : 'TBD');
                    
                    // Bangun map hari secara dinamis dari header
                    $hariMap = [];
                    foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $hari) {
                        $idx = array_search($hari, $row);
                        if ($idx !== false) {
                            $hariMap[$idx] = $hari;
                        }
                    }
                    continue;
                }

                // Jika belum ketemu header sama sekali, lewati baris ini (mencegah salah baca format lain)
                if (!$validFormatFound) continue;

                $jamColumn = '';
                foreach ($row as $col) {
                    if (is_string($col) && str_contains($col, '-') && (str_contains($col, '.') || str_contains($col, ':'))) {
                        $jamColumn = $col;
                        break;
                    }
                }

                if ($jamColumn != '') {
                    $jamRaw = explode('-', $jamColumn);
                    $jamMulai = str_replace('.', ':', trim($jamRaw[0]));
                    $jamSelesai = str_replace('.', ':', trim($jamRaw[1]));

                    foreach ($hariMap as $colIndex => $namaHari) {
                        $matkul = isset($row[$colIndex]) ? trim($row[$colIndex]) : '';

                        if ($matkul !== '') {
                            if (isset($pendingSchedules[$namaHari]) &&
                                $pendingSchedules[$namaHari]['mata_kuliah'] === $matkul &&
                                $pendingSchedules[$namaHari]['nama_asisten'] === trim($currentAssistant)) {
                                $pendingSchedules[$namaHari]['jam_selesai'] = $jamSelesai;
                            } else {
                                $flushSchedule($namaHari);

                                $pendingSchedules[$namaHari] = [
                                    'nama_asisten' => trim($currentAssistant),
                                    'hari'         => $namaHari,
                                    'jam_mulai'    => $jamMulai,
                                    'jam_selesai'  => $jamSelesai,
                                    'mata_kuliah'  => $matkul,
                                ];
                            }
                        } else {
                            $flushSchedule($namaHari);
                        }
                    }
                }
            }

            foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $h) {
                $flushSchedule($h);
            }
        }

        if ($count === 0) {
            return back()->with('error', 'Peringatan: Format file salah atau tidak sesuai dengan template Excel yang ditentukan!');
        }

        return redirect('/spv/asisten')->with('success', 'Data asisten berhasil diimport!');
    }

    public function getDetailAsisten(Request $request)
    {
        $nama = $request->query('nama');
        if (!$nama) return response()->json([]);
        return response()->json(AssistantSchedule::where('nama_asisten', $nama)->orderBy('hari', 'asc')->get());
    }
    public function clearAsistenSchedule()
    {
    \App\Models\AssistantSchedule::all()->each->delete();

    return back()->with('success', ' Wusss! Semua data jadwal asisten berhasil dihapus beserta jadwal praktikum terkait.');
    }





      public function manajemenasisten(Request $request)
{
    //  FORMAT SAKTI: Semua jeda pas 5 menit, jam asli lu gak ada yang kehapus!
    $timeSlots = [
        ['start' => '07:10', 'end' => '08:00', 'label' => '07.10-08.00'],
        ['start' => '08:00', 'end' => '08:50', 'label' => '08.00-08.50'],
        ['start' => '08:55', 'end' => '09:40', 'label' => '08.55-09.40'],
        ['start' => '09:45', 'end' => '10:35', 'label' => '09.45-10.35'],
        ['start' => '10:40', 'end' => '11:30', 'label' => '10.40-11.30'],
        ['start' => '11:35', 'end' => '12:25', 'label' => '11.35-12.25'],
        ['start' => '12:30', 'end' => '13:20', 'label' => '12.30-13.20'],
        ['start' => '13:25', 'end' => '14:15', 'label' => '13.25-14.15'],
        ['start' => '14:20', 'end' => '15:10', 'label' => '14.20-15.10'],
        ['start' => '15:15', 'end' => '16:05', 'label' => '15.15-16.05'],
        ['start' => '16:10', 'end' => '17:00', 'label' => '16.10-17.00'],
        ['start' => '17:05', 'end' => '17:55', 'label' => '17.05-17.55'],
        ['start' => '18:00', 'end' => '18:50', 'label' => '18.00-18.50'],
        ['start' => '18:45', 'end' => '20:40', 'label' => '18.45-20.40'],
    ];

    $dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

    $ruangRAObj = \App\Models\Lab::firstOrCreate(
        ['nama_lab' => 'RUANG ASISTEN'],
        ['kapasitas' => 0, 'fasilitas' => '-']
    );

    $coursesBySlot = [];
    $allSchedules = \App\Models\Schedule::with('assistants')->where('id_lab', '!=', $ruangRAObj->id_lab)->get();

    foreach ($allSchedules as $s) {
        $dayLower  = strtolower($s->hari);
        $itemStart = substr($s->jam_mulai, 0, 5);
        $itemEnd   = substr($s->jam_selesai, 0, 5);

        foreach ($timeSlots as $ts) {
            if ($itemStart < $ts['end'] && $itemEnd > $ts['start']) {
                $coursesBySlot[$dayLower][$ts['start']][] = $s->matkul;
            }
        }
    }

    $all_asisten = \App\Models\AssistantSchedule::distinct()
        ->whereNotNull('nama_asisten')
        ->where('nama_asisten', '!=', '')
        ->pluck('nama_asisten')
        ->toArray();

    $selectedAsisten = $request->query('view_asisten');

    $weeklyClasses = collect();
    $assistantAllSchedules = collect();

    if ($selectedAsisten) {
        $weeklyClasses = \App\Models\AssistantSchedule::where('nama_asisten', $selectedAsisten)
            ->where('mata_kuliah', '!=', '-')
            ->where('mata_kuliah', '!=', '')
            ->get();

        $assistantAllSchedules = \App\Models\Schedule::with(['lab', 'assistants'])
            ->whereHas('assistants', function($q) use ($selectedAsisten) {
                $q->where('nama_asisten', $selectedAsisten);
            })
            ->get();
    }

    return view('spv.jasis', compact(
        'timeSlots',
        'dayNames',
        'coursesBySlot',
        'all_asisten',
        'selectedAsisten',
        'weeklyClasses',
        'assistantAllSchedules'
    ));
}


public function updateMatrixRA(Request $request)
{
    $request->validate([
        'nama_asisten' => 'required|string',
        'cells'        => 'array',
        'old_cells'    => 'array'
    ]);

    $nama = $request->nama_asisten;
    $cells = $request->input('cells', []);
    $oldCells = $request->input('old_cells', []);

    //  SAMAKAN DENGAN GET: Masukkan penengah waktu
    $slotEnds = [
        '07:10' => '08:00', '08:00' => '08:50', '08:55' => '09:40', '09:45' => '10:35',
        '10:40' => '11:30', '11:35' => '12:25', '12:30' => '13:20', '13:25' => '14:15',
        '14:20' => '15:10', '15:15' => '16:05', '16:10' => '17:00', '17:05' => '17:55',
        '18:00' => '18:50', '18:45' => '20:40'
    ];

    DB::beginTransaction();

    try {
        $asistenObj = AssistantSchedule::firstOrCreate(
            ['nama_asisten' => $nama],
            ['hari' => '-', 'jam_mulai' => '00:00', 'jam_selesai' => '00:00', 'mata_kuliah' => '-']
        );
        $ruangRAObj = Lab::firstOrCreate(['nama_lab' => 'RUANG ASISTEN'], ['kapasitas' => 0, 'fasilitas' => '-']);

        $allAsistenIds = AssistantSchedule::where('nama_asisten', $nama)->pluck('id_asisten')->toArray();

        foreach ($cells as $day => $slots) {
            $raChanged = false;
            ksort($slots);

            // Cek perubahan RA
            foreach ($slots as $jamMulai => $statusBaru) {
                $statusLama = $oldCells[$day][$jamMulai] ?? 'KOSONG';
                if ($statusLama === 'RA' || $statusBaru === 'RA') {
                    $raChanged = true;
                }
            }

            // ==========================================
            // PROSES GABUNG & SIMPAN RA KE DATABASE
            // (Dipindah ke atas agar jadwal Lab bisa mendeteksi RA baru yang terbentuk)
            // ==========================================
            if ($raChanged) {
                // Hapus RA yang lama (cari jadwal RA yang ada asisten ini via pivot)
                $oldRASchedules = Schedule::whereHas('assistants', function($q) use ($allAsistenIds) {
                        $q->whereIn('assistant_schedules.id_asisten', $allAsistenIds);
                    })
                    ->whereRaw('LOWER(hari) = ?', [strtolower($day)])
                    ->where('id_lab', $ruangRAObj->id_lab)
                    ->get();

                foreach ($oldRASchedules as $oldRA) {
                    $oldRA->assistants()->detach($allAsistenIds);
                    // Hapus jadwal jika tidak ada asisten lain yang terhubung
                    if ($oldRA->assistants()->count() === 0) {
                        $oldRA->delete();
                    }
                }

                $raBlocks = [];
                $currentBlock = null;

                foreach ($slots as $jamMulai => $status) {
                    if ($status === 'RA') {
                        $jamSelesai = $slotEnds[$jamMulai];

                        if (!$currentBlock) {
                            $currentBlock = ['start' => $jamMulai, 'end' => $jamSelesai];
                        } else {
                            $currEndTs = strtotime($currentBlock['end']);
                            $nextStartTs = strtotime($jamMulai);
                            $diffMinutes = round(($nextStartTs - $currEndTs) / 60);

                            // GABUNG JADI SATU BARIS PANJANG
                            if ($diffMinutes <= 5) {
                                $currentBlock['end'] = $jamSelesai;
                            } else {
                                $raBlocks[] = $currentBlock;
                                $currentBlock = ['start' => $jamMulai, 'end' => $jamSelesai];
                            }
                        }
                    } else {
                        if ($currentBlock) {
                            $raBlocks[] = $currentBlock;
                            $currentBlock = null;
                        }
                    }
                }
                if ($currentBlock) {
                    $raBlocks[] = $currentBlock;
                }

                $period = CarbonPeriod::create(Carbon::now(), Carbon::now()->addMonths(6));

                foreach ($raBlocks as $block) {
                    $bStart = $block['start'];
                    $bEnd = $block['end'];

                    // 🚨 PROTEKSI 1: Cek apakah RA menabrak Lab Asli (via pivot)
                    $isBusyInLab = Schedule::whereHas('assistants', function($q) use ($allAsistenIds) {
                            $q->whereIn('assistant_schedules.id_asisten', $allAsistenIds);
                        })
                        ->whereRaw('LOWER(hari) = ?', [strtolower($day)])
                        ->where('id_lab', '!=', $ruangRAObj->id_lab)
                        ->whereRaw('LEFT(jam_mulai, 5) < ?', [$bEnd])
                        ->whereRaw('LEFT(jam_selesai, 5) > ?', [$bStart])
                        ->exists();

                    if ($isBusyInLab) {
                        throw new \Exception("Gagal Set RA di jam {$bStart}-{$bEnd}! Asisten sedang bertugas di LAB tersebut.");
                    }

                    // 🚨 PROTEKSI 2: Cek apakah RA menabrak Kuliah Pribadi
                    $bentrokKuliahRA = AssistantSchedule::where('nama_asisten', $nama)
                        ->where('mata_kuliah', '!=', '-')
                        ->where('mata_kuliah', '!=', '')
                        ->whereRaw('LOWER(hari) = ?', [strtolower($day)])
                        ->whereRaw('LEFT(jam_mulai, 5) < ?', [$bEnd])
                        ->whereRaw('LEFT(jam_selesai, 5) > ?', [$bStart])
                        ->exists();

                    if ($bentrokKuliahRA) {
                        throw new \Exception("Gagal menugaskan RA di jam {$bStart}-{$bEnd}! BENTROK dengan jam kuliah pribadi asisten.");
                    }

                    $durationMins = round((strtotime($bEnd) - strtotime($bStart)) / 60);
                    $calculatedSks = max(1, round($durationMins / 50));

                    foreach ($period as $date) {
                        if (strtolower($date->locale('id')->translatedFormat('l')) === strtolower($day)) {
                            $raSchedule = Schedule::create([
                                'tanggal'     => $date->format('Y-m-d'),
                                'hari'        => $day,
                                'id_lab'      => $ruangRAObj->id_lab,
                                'jam_mulai'   => $bStart,
                                'jam_selesai' => $bEnd,
                                'matkul'      => 'RA',
                                'sks'         => $calculatedSks,
                                'dosen'       => '-',
                            ]);
                            // Attach asisten via pivot table
                            $raSchedule->assistants()->attach($asistenObj->id_asisten);
                        }
                    }
                }
            }

            // ==========================================
            // PROSES MATKUL PRAKTIKUM LAB
            // ==========================================
            foreach ($slots as $jamMulai => $statusBaru) {
                $statusLama = $oldCells[$day][$jamMulai] ?? 'KOSONG';

                if ($statusBaru === $statusLama || $statusLama === 'KULIAH_SENDIRI' || $statusBaru === 'KULIAH_SENDIRI') {
                    continue;
                }

                // Hapus penugasan lab yang lama (detach asisten dari jadwal via pivot)
                if ($statusLama !== 'KOSONG' && $statusLama !== 'RA') {
                    $oldLabSchedules = Schedule::whereHas('assistants', function($q) use ($allAsistenIds) {
                            $q->whereIn('assistant_schedules.id_asisten', $allAsistenIds);
                        })
                        ->whereRaw('LOWER(hari) = ?', [strtolower($day)])
                        ->where('matkul', $statusLama)
                        ->where('id_lab', '!=', $ruangRAObj->id_lab)
                        ->get();

                    foreach ($oldLabSchedules as $oldLab) {
                        $oldLab->assistants()->detach($allAsistenIds);
                    }
                }

                // Masukkan asisten ke penugasan lab yang baru (attach via pivot)
                if ($statusBaru !== 'KOSONG' && $statusBaru !== 'RA') {
                    $targetMatkulSchedules = Schedule::where('matkul', $statusBaru)
                        ->whereRaw('LOWER(hari) = ?', [strtolower($day)])
                        ->where('id_lab', '!=', $ruangRAObj->id_lab)
                        ->get();

                    $firstTarget = $targetMatkulSchedules->first();

                    if ($firstTarget) {
                        $timeStart = substr($firstTarget->jam_mulai, 0, 5);
                        $timeEnd   = substr($firstTarget->jam_selesai, 0, 5);

                        // 🚨 PROTEKSI 3: Cek apakah Praktikum menabrak RA yang baru dibuat (via pivot)
                        $bentrokLabRA = Schedule::whereHas('assistants', function($q) use ($allAsistenIds) {
                                $q->whereIn('assistant_schedules.id_asisten', $allAsistenIds);
                            })
                            ->whereRaw('LOWER(hari) = ?', [strtolower($day)])
                            ->where('id_lab', $ruangRAObj->id_lab)
                            ->whereRaw('LEFT(jam_mulai, 5) < ?', [$timeEnd])
                            ->whereRaw('LEFT(jam_selesai, 5) > ?', [$timeStart])
                            ->exists();

                        if ($bentrokLabRA) {
                             throw new \Exception("Gagal! Durasi praktikum {$statusBaru} ({$timeStart}-{$timeEnd}) BENTROK dengan jadwal Jaga RA asisten.");
                        }

                        // 🚨 PROTEKSI 4: Cek apakah Praktikum menabrak Kuliah Pribadi
                        $bentrokKuliahLab = AssistantSchedule::where('nama_asisten', $nama)
                            ->where('mata_kuliah', '!=', '-')
                            ->where('mata_kuliah', '!=', '')
                            ->whereRaw('LOWER(hari) = ?', [strtolower($day)])
                            ->whereRaw('LEFT(jam_mulai, 5) < ?', [$timeEnd])
                            ->whereRaw('LEFT(jam_selesai, 5) > ?', [$timeStart])
                            ->exists();

                        if ($bentrokKuliahLab) {
                            throw new \Exception("Gagal! Durasi praktikum {$statusBaru} BENTROK dengan jadwal kuliah asisten.");
                        }

                        // Attach asisten ke semua jadwal matkul tersebut (multi-asisten support)
                        foreach ($targetMatkulSchedules as $targetSchedule) {
                            $targetSchedule->assistants()->syncWithoutDetaching([$asistenObj->id_asisten]);
                        }
                    }
                }
            }
        }

        DB::commit();
        return back()->with('success', '💾 Penugasan asisten relasional berhasil disinkronkan.');
    } catch (\Exception $e) {
        DB::rollBack();
        return back()->with('error', 'Peringatan Sistem: ' . $e->getMessage());
    }
}


    public function inputMatrix()
{
    // 1. Ambil nama user yang login & paksa kapital semua biar sinkron di DB
    $namaUser = strtoupper(trim(auth()->user()->name ?? auth()->user()->nama ?? 'ASISTEN'));

    // 2. Ambil data untuk dimasukkan ke dalam kotak-kotak form atas (Grouped berdasarkan hari)
    $existingSchedules = \App\Models\AssistantSchedule::where('nama_asisten', $namaUser)
        ->where('mata_kuliah', '!=', '-')
        ->get()
        ->groupBy('hari');

    // 3. Ambil data flat untuk tabel riwayat di bawah (Diurutkan Senin s/d Jumat + jam mulai)
    $dayOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    $savedSchedulesFlat = \App\Models\AssistantSchedule::where('nama_asisten', $namaUser)
        ->where('mata_kuliah', '!=', '-')
        ->get()
        ->sortBy(function($item) use ($dayOrder) {
            return array_search($item->hari, $dayOrder) . '-' . $item->jam_mulai;
        });

    // 🌟 SINKRONISASI: Lempar variabel ke view 'asisten.input' sesuai stack trace lu!
    return view('asisten.input', compact('existingSchedules', 'savedSchedulesFlat'));
}


   public function storsis(Request $request)
{
    // Validasi paket data dari AJAX Fetch
    $request->validate([
        'jadwal' => 'required|array'
    ]);

    // Ambil nama dari user login & paksa KAPITAL SEMUA biar sinkron di DB
    $namaUser = strtoupper(trim(auth()->user()->name ?? auth()->user()->nama ?? 'ASISTEN'));
    $jadwal = $request->input('jadwal', []);

    DB::beginTransaction();
    try {
        // 1. Bersihkan jadwal kuliah lama asisten ini (Kecuali data dummy relasi '-')
        \App\Models\AssistantSchedule::where('nama_asisten', $namaUser)
            ->where('mata_kuliah', '!=', '-')
            ->delete();

        // 2. Looping per Hari dan per Matkul yang dikirim dari Frontend
        foreach ($jadwal as $hari => $matkuls) {
            if (!is_array($matkuls)) continue;

            foreach ($matkuls as $item) {
                // Pastikan baris data tidak kosong sebelum di-insert
                if (!empty($item['name']) && !empty($item['start']) && !empty($item['end'])) {

                    // Format Matkul + SKS karena di DB lu gak ada kolom SKS tersendiri
                    $matkulDenganSks = strtoupper(trim($item['name'])) . ' (' . ($item['sks'] ?? '2 SKS') . ')';

                    \App\Models\AssistantSchedule::create([
                        'nama_asisten' => $namaUser,
                        'hari'         => $hari,
                        'jam_mulai'    => $item['start'],
                        'jam_selesai'  => $item['end'],
                        'mata_kuliah'  => $matkulDenganSks,
                    ]);
                }
            }
        }

        // 3. Pastikan data pancingan (dummy) root asisten tetap eksis untuk relasi matriks RA
        \App\Models\AssistantSchedule::firstOrCreate(
            ['nama_asisten' => $namaUser, 'mata_kuliah' => '-'],
            ['hari' => '-', 'jam_mulai' => '00:00', 'jam_selesai' => '00:00']
        );

        DB::commit();
        return response()->json([
            'success' => true,
            'message' => 'Jadwal kuliah berhasil disinkronkan ke database!'
        ]);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Gagal menyimpan data: ' . $e->getMessage()
        ], 500);
    }
}

public function hapusJadwal($id)
{
    // Cari jadwal berdasarkan id primary key-nya (id_asisten)
    $jadwal = AssistantSchedule::findOrFail($id);

    // 🔒 PROTEKSI SAKTI: Biar asisten nakal gak bisa ngehapus jadwal milik asisten lain lewat inspect element
    $namaUser = strtoupper(trim(auth()->user()->name ?? auth()->user()->nama ?? 'ASISTEN'));
    if (strtoupper(trim($jadwal->nama_asisten)) !== $namaUser) {
        return back()->with('error', 'Waduh, Anda tidak punya akses untuk menghapus jadwal asisten lain!');
    }

    $jadwal->delete();

    return back()->with('success', 'Jadwal kuliah berhasil dihapus dari sistem.');
}


public function putsen()
{
    // 1. Ambil nama user yang login & paksa kapital
    $namaUser = strtoupper(trim(auth()->user()->name ?? auth()->user()->nama ?? 'ASISTEN'));

    // 2. Ambil data untuk dimasukkan ke dalam kotak-kotak form atas (Grouped)
    $existingSchedules = \App\Models\AssistantSchedule::where('nama_asisten', $namaUser)
        ->where('mata_kuliah', '!=', '-')
        ->get()
        ->groupBy('hari');

    // 3. Ambil data flat untuk tabel riwayat di bawah (Sorted Senin s/d Jumat)
    $dayOrder = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];
    $savedSchedulesFlat = \App\Models\AssistantSchedule::where('nama_asisten', $namaUser)
        ->where('mata_kuliah', '!=', '-')
        ->get()
        ->sortBy(function($item) use ($dayOrder) {
            return array_search($item->hari, $dayOrder) . '-' . $item->jam_mulai;
        });

    return view('asisten.form_jadwal_asisten', compact('existingSchedules', 'savedSchedulesFlat'));
}

public function cetakMatriks() {
        // 1. Ambil nama asisten
        $nama = strtoupper(trim(auth()->user()->name ?? auth()->user()->nama ?? 'ASISTEN'));

        // 2. Ambil jadwal kuliah (AssistantSchedule)
        $weeklyClasses = \App\Models\AssistantSchedule::where('nama_asisten', $nama)
            ->where('mata_kuliah', '!=', '-')
            ->get();

        // 🌟 SAKTI 1: Tarik data jadwal penugasan beserta data LAB-nya
        // Kita cari id_asisten yang nama_asisten-nya cocok dengan yang lagi login
        $assistantAllSchedules = \App\Models\Schedule::with(['lab', 'assistants'])
            ->whereHas('assistants', function($query) use ($nama) {
                $query->where('nama_asisten', $nama);
            })
            ->get();

        $dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'];

        // 4. Array Slot Jam
        $timeSlotsRaw = [
            '07:10' => '08:00', '08:00' => '08:50', '08:55' => '09:40', '09:45' => '10:35',
            '10:40' => '11:30', '11:35' => '12:25', '12:30' => '13:20', '13:25' => '14:15',
            '14:20' => '15:10', '15:15' => '16:05', '16:10' => '17:00', '17:05' => '17:55',
            '18:00' => '18:50', '18:45' => '20:40'
        ];

        // 5. Format ulang jam
        $timeSlots = [];
        foreach ($timeSlotsRaw as $start => $end) {
            $timeSlots[] = [
                'start' => $start,
                'end'   => $end,
                'label' => $start . ' - ' . $end
            ];
        }

        // 6. Generate PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('asisten.cetak', compact('nama', 'timeSlots', 'dayNames', 'weeklyClasses', 'assistantAllSchedules'))
                  ->setPaper('a4', 'landscape');

        // 🌟 SAKTI 3: Bersihkan nama dari karakter aneh (termasuk / dan \) biar aman jadi nama file
        $namaAman = \Illuminate\Support\Str::slug($nama, '_');

        return $pdf->stream('Jadwal_Kerja_' . $namaAman . '.pdf');
    }
}


