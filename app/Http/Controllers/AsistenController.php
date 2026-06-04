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
    // 1. Validasi super ketat agar data yang masuk tidak kosong/cacat
    $request->validate([
        'nama_asisten' => 'required|string|max:255',
        'hari'         => 'required|string',
        'jam_mulai'    => 'required',
        'jam_selesai'  => 'required',
        'mata_kuliah'  => 'required|string|max:255',
    ]);

    // 2. Cari data berdasarkan ID
    // ⚠️ CATATAN: Jika nama primary key di database lu 'id_asisten', ganti 'id' di bawah menjadi 'id_asisten'
    $jadwal = AssistantSchedule::where('id_asisten', $id)->first();

    // Jaga-jaga kalau data tidak ketemu di database
    if (!$jadwal) {
        return back()->with('error', 'Gagal! Data jadwal asisten tidak ditemukan.');
    }

    // 3. Eksekusi update data dari form HTML ke kolom database
    $jadwal->nama_asisten = $request->nama_asisten;
    $jadwal->hari         = $request->hari;
    $jadwal->jam_mulai    = $request->jam_mulai;
    $jadwal->jam_selesai  = $request->jam_selesai;
    $jadwal->mata_kuliah  = $request->mata_kuliah;

    // 4. KUNCI PERUBAHAN (Sapu bersih, paksa masuk database)
    $jadwal->save();

    // 5. Kembalikan user ke halaman dengan status sukses
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
            'file_asisten' => 'required|mimes:xlsx,xls,csv|max:5120'
        ]);

        $sheets = Excel::toArray([], $request->file('file_asisten'));
        $count = 0;

        foreach ($sheets as $rows) {
            $currentAssistant = 'TBD';
            $pendingSchedules = [];

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
                if ($seninIndex !== false) {
                    foreach (['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat'] as $h) {
                        $flushSchedule($h);
                    }
                    $currentAssistant = !empty($row[0]) ? $row[0] : (!empty($row[1]) ? $row[1] : 'TBD');
                    continue;
                }

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

                    $hariMap = [
                        2 => 'Senin',
                        3 => 'Selasa',
                        4 => 'Rabu',
                        5 => 'Kamis',
                        6 => 'Jumat'
                    ];

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
            return back()->with('error', 'Gagal! Format tidak dikenali atau file kosong.');
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

    return back()->with('success', '🧹 Wusss! Semua data jadwal asisten berhasil dihapus beserta jadwal praktikum terkait.');
    }



    //ngasih jadwal//

    public function manajemenasisten(Request $request)
    {
        $labs = Lab::all();
        $filterDate = $request->get('filter_date', date('Y-m-d'));
        $schedules = Schedule::with(['lab', 'assistantSchedule'])->where('tanggal', $filterDate)->orderBy('jam_mulai', 'asc')->get();

        $all_asisten = AssistantSchedule::whereNotNull('nama_asisten')->where('nama_asisten', '!=', '')->distinct()->pluck('nama_asisten')->toArray();
        sort($all_asisten);

        $rawMatrixCourses = Schedule::with('lab')->whereNotNull('matkul')->where('matkul', '!=', '')->get();

        $coursesBySlot = [];
        foreach ($rawMatrixCourses as $mc) {
            $dayKey = strtolower($mc->hari);
            $timeKey = substr($mc->jam_mulai, 0, 5); 
            $coursesBySlot[$dayKey][$timeKey][] = $mc->matkul;
        }

        $selectedAsisten = $request->get('view_asisten');
        $dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $weeklyClasses = collect();
        $assistantAllSchedules = collect();

        if ($selectedAsisten) {
            $weeklyClasses = AssistantSchedule::where('nama_asisten', $selectedAsisten)->get();
            $assistantAllSchedules = Schedule::with(['lab'])->whereHas('assistantSchedule', function($q) use ($selectedAsisten){
                $q->where('nama_asisten', $selectedAsisten);
            })->get();
        }

        $timeSlots = [
            ['start' => '08:00', 'end' => '08:50', 'label' => '08.00-08.50'],
            ['start' => '08:55', 'end' => '09:45', 'label' => '08.55-09.45'],
            ['start' => '09:50', 'end' => '10:40', 'label' => '09.50-10.40'],
            ['start' => '10:45', 'end' => '11:35', 'label' => '10.45-11.35'],
            ['start' => '12:30', 'end' => '13:20', 'label' => '12.30-13.20'], 
            ['start' => '13:25', 'end' => '14:15', 'label' => '13.25-14.15'],
            ['start' => '14:20', 'end' => '15:10', 'label' => '14.20-15.10'],
            ['start' => '15:15', 'end' => '16:05', 'label' => '15.15-16.05'],
            ['start' => '16:10', 'end' => '17:00', 'label' => '16.10-17.00'],
            ['start' => '18:00', 'end' => '18:50', 'label' => '18.00-18.50'],
            ['start' => '18:55', 'end' => '19:45', 'label' => '18.55-19.45'],
            ['start' => '19:50', 'end' => '20:40', 'label' => '19.50-20.40'],
            ['start' => '20:45', 'end' => '21:35', 'label' => '20.45-21.35'],
        ];

        return view('spv.jasis', compact('labs', 'schedules', 'filterDate', 'dayNames', 'all_asisten', 'coursesBySlot', 'weeklyClasses', 'assistantAllSchedules', 'timeSlots', 'selectedAsisten'));
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

        $slotEnds = [
            '08:00' => '08:50', '08:55' => '09:45', '09:50' => '10:40', '10:45' => '11:35',
            '12:30' => '13:20', '13:25' => '14:15', '14:20' => '15:10', '15:15' => '16:05', 
            '16:10' => '17:00', '18:00' => '18:50', '18:55' => '19:45', '19:50' => '20:40', '20:45' => '21:35'
        ];

        DB::beginTransaction();

        try {
            $asistenObj = AssistantSchedule::firstOrCreate(
                ['nama_asisten' => $nama], 
                ['hari' => '-', 'jam_mulai' => '00:00', 'jam_selesai' => '00:00', 'mata_kuliah' => '-']
            );
            $ruangRAObj = Lab::firstOrCreate(['nama_lab' => 'RUANG RA'], ['kapasitas' => 0, 'fasilitas' => '-']);

            // Mengambil semua ID untuk antisipasi duplikat
            $allAsistenIds = AssistantSchedule::where('nama_asisten', $nama)->pluck('id_asisten')->toArray();

            foreach ($cells as $day => $slots) {
                foreach ($slots as $jamMulai => $statusBaru) {
                    
                    $statusLama = $oldCells[$day][$jamMulai] ?? 'KOSONG';

                    if ($statusBaru === $statusLama || $statusLama === 'KULIAH_SENDIRI' || $statusBaru === 'KULIAH_SENDIRI') {
                        continue;
                    }

                    $jamSelesai = $slotEnds[$jamMulai] ?? '00:00';

                    // 🔥 FIX 1: Logika Pelepasan Tugas Lama yang benar
                    if ($statusLama !== 'KOSONG' && $statusLama !== 'RA') {
                        // Lepas tugas dari matkul (Ubah jadi NULL)
                        Schedule::whereIn('id_asisten', $allAsistenIds)
                            ->whereRaw('LOWER(hari) = ?', [strtolower($day)])
                            ->whereRaw('LEFT(jam_mulai, 5) = ?', [$jamMulai])
                            ->where('id_lab', '!=', $ruangRAObj->id_lab)
                            ->update(['id_asisten' => null]); 

                    } elseif ($statusLama === 'RA') {
                        // Hapus jadwal RA khusus
                        Schedule::whereIn('id_asisten', $allAsistenIds)
                            ->whereRaw('LOWER(hari) = ?', [strtolower($day)])
                            ->whereRaw('LEFT(jam_mulai, 5) = ?', [$jamMulai])
                            ->where('id_lab', $ruangRAObj->id_lab)
                            ->delete();
                    }

                    // 🔥 FIX 2: Logika Penugasan Baru
                    if ($statusBaru === 'RA') {
                        $period = CarbonPeriod::create(Carbon::now(), Carbon::now()->addMonths(6));

                        foreach ($period as $date) {
                            if (strtolower($date->locale('id')->translatedFormat('l')) === strtolower($day)) {
                                Schedule::create([
                                    'tanggal'      => $date->format('Y-m-d'),
                                    'hari'         => $day,
                                    'id_lab'       => $ruangRAObj->id_lab,
                                    'id_asisten'   => $asistenObj->id_asisten,
                                    'jam_mulai'    => $jamMulai,
                                    'jam_selesai'  => $jamSelesai,
                                    'matkul'       => 'RA',
                                    'sks'          => 1,
                                    'dosen'        => '-',
                                ]);
                            }
                        }
                    } elseif ($statusBaru !== 'KOSONG') {
                        // Tugaskan asisten ini ke jadwal matkul yang baru dipilih
                        Schedule::where('matkul', $statusBaru)
                            ->whereRaw('LOWER(hari) = ?', [strtolower($day)])
                            ->whereRaw('LEFT(jam_mulai, 5) = ?', [$jamMulai])
                            ->where('id_lab', '!=', $ruangRAObj->id_lab)
                            ->update(['id_asisten' => $asistenObj->id_asisten]);
                    }
                }
            }

            DB::commit(); 
            return back()->with('success', '💾 Penugasan asisten relasional berhasil disinkronkan.');
        } catch (\Exception $e) {
            DB::rollBack(); 
            return back()->with('error', 'Waduh gagal Bre: ' . $e->getMessage());
        }
    }
    /////////////role asisten/////
   
    /**
     * 1. Menampilkan Halaman Matrix Input Jadwal (Untuk Asisten)
     */
    public function inputMatrix()
    {
        // Langsung arahkan ke file view blade yang udah lu copas sebelumnya
        // Pastikan file bladenya ada di resources/views/asisten/input-matrix.blade.php
        return view('asisten.input');
    }

    /**
     * 2. Memproses Data Matrix yang Dikirim Asisten ke Database
     */
    public function storsis(Request $request)
    {
      $request->validate([
            'nama_asisten' => 'required|string',
            'hari'         => 'required|string',
            'jam_mulai'    => 'required|string|size:5',
            'sks'          => 'required|integer',
            'mata_kuliah'  => 'required|string',
        ]);

        // Hitung Jam Selesai (1 SKS = 50 Menit)
        $jamMulai = Carbon::createFromFormat('H:i', $request->jam_mulai);
        $totalMenit = $request->sks * 50;
        $jamSelesai = $jamMulai->copy()->addMinutes($totalMenit)->format('H:i');

        AssistantSchedule::create([
            'nama_asisten' => strtoupper(trim($request->nama_asisten)),
            'hari'         => $request->hari,
            'jam_mulai'    => $request->jam_mulai,
            'jam_selesai'  => $jamSelesai,
            'mata_kuliah'  => trim($request->mata_kuliah),
        ]);

        // Pakai session biar nama dan hari terakhir kesimpen, asisten ga usah milih ulang
        return back()
            ->with('success', 'Jadwal ' . $request->mata_kuliah . ' berhasil ditambahkan!')
            ->with('last_asisten', $request->nama_asisten)
            ->with('last_hari', $request->hari);}

            public function hapusJadwal($id)
    {
        // Hapus berdasarkan Primary Key tabel lu 
        // (Pastikan nama variable primary key lu sesuai, di model lu primaryKey = 'id_asisten')
        AssistantSchedule::where('id_asisten', $id)->delete();

        return back()->with('success', 'Jadwal berhasil dihapus!');
    }

}


    