<?php

namespace App\Http\Controllers;

use App\Models\AssistantSchedule;
use App\Models\Lab;
use App\Models\Schedule;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AsistenController extends Controller
{
    public function jadwalAsisten(Request $request)
    {
        $semuaAsisten = AssistantSchedule::select('nama_asisten')->distinct()->get();

        $namaDicari = $request->query('nama');
        $hariDicari = $request->query('hari');

        if ($namaDicari || $hariDicari) {
            $query = AssistantSchedule::query();

            if ($namaDicari) {
                $query->where('nama_asisten', $namaDicari);
            }

            if ($hariDicari) {
                $query->where('hari', $hariDicari);
            }

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
            'hari' => 'required|string',
            'jam_mulai' => 'required',
            'sks' => 'required|numeric',
            'mata_kuliah' => 'required|string',
        ]);

        $menit = $request->sks * 50;
        $jamSelesai = date('H:i', strtotime($request->jam_mulai . " + $menit minutes"));

        AssistantSchedule::create([
            'nama_asisten' => $request->nama_asisten,
            'hari' => $request->hari,
            'jam_mulai' => $request->jam_mulai,
            'jam_selesai' => $jamSelesai,
            'mata_kuliah' => $request->mata_kuliah,
        ]);

        return back()->with('success', 'Jadwal mata kuliah baru berhasil ditambahkan!');
    }

    public function updateAsisten(Request $request, $id)
    {
        $request->validate([
            'nama_asisten' => 'required|string|max:255',
            'hari' => 'required|string',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'mata_kuliah' => 'required|string|max:255',
        ]);

        $jadwal = AssistantSchedule::where('id_asisten', $id)->first();

        if (!$jadwal) {
            return back()->with('error', 'Gagal! Data jadwal asisten tidak ditemukan.');
        }

        $jadwal->nama_asisten = $request->nama_asisten;
        $jadwal->hari = $request->hari;
        $jadwal->jam_mulai = $request->jam_mulai;
        $jadwal->jam_selesai = $request->jam_selesai;
        $jadwal->mata_kuliah = $request->mata_kuliah;
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
            'file_asisten' => 'required|mimes:xlsx,xls,csv|max:5120',
        ]);

        $sheets = Excel::toArray([], $request->file('file_asisten'));
        $count = 0;

        foreach ($sheets as $rows) {
            $currentAssistant = 'TBD';
            $pendingSchedules = [];

            $flushSchedule = function ($hari) use (&$pendingSchedules, &$count) {
                if (isset($pendingSchedules[$hari])) {
                    AssistantSchedule::create($pendingSchedules[$hari]);
                    $count++;
                    unset($pendingSchedules[$hari]);
                }
            };

            foreach ($rows as $row) {
                if (empty(array_filter($row))) {
                    continue;
                }

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

                if ($jamColumn !== '') {
                    $jamRaw = explode('-', $jamColumn);
                    $jamMulai = str_replace('.', ':', trim($jamRaw[0]));
                    $jamSelesai = str_replace('.', ':', trim($jamRaw[1]));

                    $hariMap = [
                        2 => 'Senin',
                        3 => 'Selasa',
                        4 => 'Rabu',
                        5 => 'Kamis',
                        6 => 'Jumat',
                    ];

                    foreach ($hariMap as $colIndex => $namaHari) {
                        $matkul = isset($row[$colIndex]) ? trim($row[$colIndex]) : '';

                        if ($matkul !== '') {
                            if (
                                isset($pendingSchedules[$namaHari]) &&
                                $pendingSchedules[$namaHari]['mata_kuliah'] === $matkul &&
                                $pendingSchedules[$namaHari]['nama_asisten'] === trim($currentAssistant)
                            ) {
                                $pendingSchedules[$namaHari]['jam_selesai'] = $jamSelesai;
                            } else {
                                $flushSchedule($namaHari);

                                $pendingSchedules[$namaHari] = [
                                    'nama_asisten' => trim($currentAssistant),
                                    'hari' => $namaHari,
                                    'jam_mulai' => $jamMulai,
                                    'jam_selesai' => $jamSelesai,
                                    'mata_kuliah' => $matkul,
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

        if (!$nama) {
            return response()->json([]);
        }

        return response()->json(
            AssistantSchedule::where('nama_asisten', $nama)->orderBy('hari', 'asc')->get()
        );
    }

    public function clearAsistenSchedule()
    {
        AssistantSchedule::all()->each->delete();

        return back()->with('success', 'Semua data jadwal asisten berhasil dihapus beserta jadwal praktikum terkait.');
    }

    public function manajemenasisten(Request $request)
    {
        $timeSlots = $this->matrixTimeSlots();
        $dayNames = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];

        $ruangRAObj = Lab::firstOrCreate(
            ['nama_lab' => 'RUANG ASISTEN'],
            ['kapasitas' => 0, 'fasilitas' => '-']
        );

        $coursesBySlot = [];
        $allSchedules = Schedule::where('id_lab', '!=', $ruangRAObj->id_lab)->get();

        foreach ($allSchedules as $s) {
            $dayLower = strtolower($s->hari);
            $itemStart = substr($s->jam_mulai, 0, 5);
            $itemEnd = substr($s->jam_selesai, 0, 5);

            foreach ($timeSlots as $ts) {
                if ($itemStart < $ts['end'] && $itemEnd > $ts['start']) {
                    $coursesBySlot[$dayLower][$ts['start']][] = $s->matkul;
                }
            }
        }

        $all_asisten = AssistantSchedule::distinct()
            ->whereNotNull('nama_asisten')
            ->where('nama_asisten', '!=', '')
            ->pluck('nama_asisten')
            ->toArray();

        $selectedAsisten = $request->query('view_asisten');

        $weeklyClasses = collect();
        $assistantAllSchedules = collect();

        if ($selectedAsisten) {
            $weeklyClasses = AssistantSchedule::where('nama_asisten', $selectedAsisten)
                ->where('mata_kuliah', '!=', '-')
                ->where('mata_kuliah', '!=', '')
                ->get();

            $allAsistenIds = AssistantSchedule::where('nama_asisten', $selectedAsisten)
                ->pluck('id_asisten')
                ->toArray();

            $assistantAllSchedules = Schedule::with('lab')
                ->whereIn('id_asisten', $allAsistenIds)
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
            'cells' => 'array',
            'old_cells' => 'array',
        ]);

        $nama = $request->nama_asisten;
        $cells = $request->input('cells', []);
        $oldCells = $request->input('old_cells', []);
        $slotEnds = collect($this->matrixTimeSlots())->pluck('end', 'start')->toArray();

        DB::beginTransaction();

        try {
            $asistenObj = AssistantSchedule::firstOrCreate(
                ['nama_asisten' => $nama],
                ['hari' => '-', 'jam_mulai' => '00:00', 'jam_selesai' => '00:00', 'mata_kuliah' => '-']
            );

            $ruangRAObj = Lab::firstOrCreate(
                ['nama_lab' => 'RUANG ASISTEN'],
                ['kapasitas' => 0, 'fasilitas' => '-']
            );

            $allAsistenIds = AssistantSchedule::where('nama_asisten', $nama)->pluck('id_asisten')->toArray();

            foreach ($cells as $day => $slots) {
                ksort($slots);
                $raChanged = false;

                foreach ($slots as $jamMulai => $statusBaru) {
                    $statusLama = $oldCells[$day][$jamMulai] ?? 'KOSONG';

                    if ($statusBaru === $statusLama || $statusLama === 'KULIAH_SENDIRI' || $statusBaru === 'KULIAH_SENDIRI') {
                        continue;
                    }

                    if ($statusLama === 'RA' || $statusBaru === 'RA') {
                        $raChanged = true;
                    }

                    if ($statusLama !== 'KOSONG' && $statusLama !== 'RA') {
                        Schedule::whereIn('id_asisten', $allAsistenIds)
                            ->whereRaw('LOWER(hari) = ?', [strtolower($day)])
                            ->where('matkul', $statusLama)
                            ->where('id_lab', '!=', $ruangRAObj->id_lab)
                            ->update(['id_asisten' => null]);
                    }

                    if ($statusBaru !== 'KOSONG' && $statusBaru !== 'RA') {
                        $targetMatkul = Schedule::where('matkul', $statusBaru)
                            ->whereRaw('LOWER(hari) = ?', [strtolower($day)])
                            ->where('id_lab', '!=', $ruangRAObj->id_lab)
                            ->first();

                        if (!$targetMatkul) {
                            continue;
                        }

                        $timeStart = substr($targetMatkul->jam_mulai, 0, 5);
                        $timeEnd = substr($targetMatkul->jam_selesai, 0, 5);

                        $bentrokKuliahLab = AssistantSchedule::where('nama_asisten', $nama)
                            ->where('mata_kuliah', '!=', '-')
                            ->where('mata_kuliah', '!=', '')
                            ->whereRaw('LOWER(hari) = ?', [strtolower($day)])
                            ->whereRaw('LEFT(jam_mulai, 5) < ?', [$timeEnd])
                            ->whereRaw('LEFT(jam_selesai, 5) > ?', [$timeStart])
                            ->exists();

                        if ($bentrokKuliahLab) {
                            throw new \Exception("Gagal! Durasi praktikum {$statusBaru} bentrok dengan jadwal kuliah asisten.");
                        }

                        $bentrokJagaLab = Schedule::whereIn('id_asisten', $allAsistenIds)
                            ->whereRaw('LOWER(hari) = ?', [strtolower($day)])
                            ->where('id_lab', '!=', $ruangRAObj->id_lab)
                            ->where('matkul', '!=', $statusBaru)
                            ->whereRaw('LEFT(jam_mulai, 5) < ?', [$timeEnd])
                            ->whereRaw('LEFT(jam_selesai, 5) > ?', [$timeStart])
                            ->first();

                        if ($bentrokJagaLab) {
                            $matkulBentrok = $bentrokJagaLab->matkul;
                            $jamBentrok = substr($bentrokJagaLab->jam_mulai, 0, 5) . '-' . substr($bentrokJagaLab->jam_selesai, 0, 5);
                            throw new \Exception("Gagal! {$statusBaru} tabrakan dengan {$matkulBentrok} ({$jamBentrok}) di hari yang sama.");
                        }

                        Schedule::where('matkul', $statusBaru)
                            ->whereRaw('LOWER(hari) = ?', [strtolower($day)])
                            ->where('id_lab', '!=', $ruangRAObj->id_lab)
                            ->update(['id_asisten' => $asistenObj->id_asisten]);
                    }
                }

                if ($raChanged) {
                    $this->syncRaBlocks($slots, $slotEnds, $day, $allAsistenIds, $ruangRAObj, $asistenObj);
                }
            }

            DB::commit();
            return back()->with('success', 'Penugasan asisten relasional berhasil disinkronkan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Waduh gagal Bre: ' . $e->getMessage());
        }
    }

    public function inputMatrix()
    {
        $namaAsisten = auth()->user()->name;
        $jadwalTersimpan = AssistantSchedule::where('nama_asisten', $namaAsisten)
            ->where('mata_kuliah', '!=', '-')
            ->orderByRaw("FIELD(hari, 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu')")
            ->orderBy('jam_mulai')
            ->get();

        return view('asisten.input', compact('namaAsisten', 'jadwalTersimpan'));
    }

    public function storsis(Request $request)
    {
        $validated = $request->validate([
            'jadwal' => 'required|array',
            'jadwal.*.*.mata_kuliah' => 'nullable|string|max:255',
            'jadwal.*.*.jam_mulai' => 'nullable|date_format:H:i',
            'jadwal.*.*.sks' => 'nullable|integer|between:1,4',
        ]);

        $namaAsisten = auth()->user()->name;
        $rows = [];

        foreach ($validated['jadwal'] as $hari => $items) {
            foreach ($items as $item) {
                $matkul = trim($item['mata_kuliah'] ?? '');
                $jamMulaiInput = $item['jam_mulai'] ?? null;
                $sks = (int) ($item['sks'] ?? 0);

                if ($matkul === '' && !$jamMulaiInput) {
                    continue;
                }

                if ($matkul === '' || !$jamMulaiInput || $sks < 1 || $sks > 4) {
                    return back()
                        ->withInput()
                        ->with('error', 'Lengkapi nama mata kuliah, jam mulai, dan SKS pada setiap baris yang diisi.');
                }

                $jamMulai = Carbon::createFromFormat('H:i', $jamMulaiInput);
                $totalMenit = ($sks * 50) + (($sks - 1) * 5);
                $jamSelesai = $jamMulai->copy()->addMinutes($totalMenit)->format('H:i');

                foreach ($rows as $existing) {
                    if (
                        strtolower($existing['hari']) === strtolower($hari) &&
                        $existing['jam_mulai'] < $jamSelesai &&
                        $existing['jam_selesai'] > $jamMulaiInput
                    ) {
                        return back()
                            ->withInput()
                            ->with('error', "Jadwal {$matkul} bentrok dengan {$existing['mata_kuliah']} pada hari {$hari}.");
                    }
                }

                $rows[] = [
                    'nama_asisten' => $namaAsisten,
                    'hari' => $hari,
                    'jam_mulai' => $jamMulaiInput,
                    'jam_selesai' => $jamSelesai,
                    'mata_kuliah' => $matkul,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        DB::beginTransaction();

        try {
            AssistantSchedule::where('nama_asisten', $namaAsisten)
                ->where('mata_kuliah', '!=', '-')
                ->delete();

            if (!empty($rows)) {
                AssistantSchedule::insert($rows);
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan jadwal: ' . $e->getMessage());
        }

        return back()->with('success', 'Jadwal kuliah berhasil disimpan.');
    }

    public function hapusJadwal($id)
    {
        $namaAsisten = auth()->user()->name;

        AssistantSchedule::where('id_asisten', $id)
            ->where('nama_asisten', $namaAsisten)
            ->delete();

        return back()->with('success', 'Jadwal berhasil dihapus!');
    }

    public function putsen()
    {
        return $this->inputMatrix();
    }

    public function storeJadwalMatrix(Request $request)
    {
        return $this->storsis($request);
    }

    private function matrixTimeSlots(): array
    {
        return [
            ['start' => '08:00', 'end' => '08:50', 'label' => '08.00-08.50'],
            ['start' => '08:55', 'end' => '09:45', 'label' => '08.55-09.45'],
            ['start' => '09:50', 'end' => '10:40', 'label' => '09.50-10.40'],
            ['start' => '10:45', 'end' => '11:35', 'label' => '10.45-11.35'],
            ['start' => '11:40', 'end' => '12:25', 'label' => '11.40-12.25'],
            ['start' => '12:30', 'end' => '13:20', 'label' => '12.30-13.20'],
            ['start' => '13:25', 'end' => '14:15', 'label' => '13.25-14.15'],
            ['start' => '14:20', 'end' => '15:10', 'label' => '14.20-15.10'],
            ['start' => '15:15', 'end' => '16:05', 'label' => '15.15-16.05'],
            ['start' => '16:10', 'end' => '17:00', 'label' => '16.10-17.00'],
            ['start' => '17:05', 'end' => '17:55', 'label' => '17.05-17.55'],
            ['start' => '18:00', 'end' => '18:50', 'label' => '18.00-18.50'],
            ['start' => '18:55', 'end' => '19:45', 'label' => '18.55-19.45'],
            ['start' => '19:50', 'end' => '20:40', 'label' => '19.50-20.40'],
            ['start' => '20:45', 'end' => '21:35', 'label' => '20.45-21.35'],
        ];
    }

    private function syncRaBlocks(array $slots, array $slotEnds, string $day, array $allAsistenIds, Lab $ruangRAObj, AssistantSchedule $asistenObj): void
    {
        Schedule::whereIn('id_asisten', $allAsistenIds)
            ->whereRaw('LOWER(hari) = ?', [strtolower($day)])
            ->where('id_lab', $ruangRAObj->id_lab)
            ->delete();

        $raBlocks = [];
        $currentBlock = null;

        foreach ($slots as $jamMulai => $status) {
            if ($status === 'RA') {
                $jamSelesai = $slotEnds[$jamMulai] ?? null;

                if (!$jamSelesai) {
                    continue;
                }

                if (!$currentBlock) {
                    $currentBlock = ['start' => $jamMulai, 'end' => $jamSelesai];
                } else {
                    $diffMinutes = round((strtotime($jamMulai) - strtotime($currentBlock['end'])) / 60);

                    if ($diffMinutes <= 5) {
                        $currentBlock['end'] = $jamSelesai;
                    } else {
                        $raBlocks[] = $currentBlock;
                        $currentBlock = ['start' => $jamMulai, 'end' => $jamSelesai];
                    }
                }
            } elseif ($currentBlock) {
                $raBlocks[] = $currentBlock;
                $currentBlock = null;
            }
        }

        if ($currentBlock) {
            $raBlocks[] = $currentBlock;
        }

        $period = CarbonPeriod::create(Carbon::now(), Carbon::now()->addMonths(6));

        foreach ($raBlocks as $block) {
            $bStart = $block['start'];
            $bEnd = $block['end'];

            $isBusyInLab = Schedule::whereIn('id_asisten', $allAsistenIds)
                ->whereRaw('LOWER(hari) = ?', [strtolower($day)])
                ->where('id_lab', '!=', $ruangRAObj->id_lab)
                ->whereRaw('LEFT(jam_mulai, 5) < ?', [$bEnd])
                ->whereRaw('LEFT(jam_selesai, 5) > ?', [$bStart])
                ->exists();

            if ($isBusyInLab) {
                throw new \Exception("Jam {$bStart}-{$bEnd} tidak bisa diisi RA karena asisten sedang mengajar LAB.");
            }

            $bentrokKuliahRA = AssistantSchedule::where('nama_asisten', $asistenObj->nama_asisten)
                ->where('mata_kuliah', '!=', '-')
                ->where('mata_kuliah', '!=', '')
                ->whereRaw('LOWER(hari) = ?', [strtolower($day)])
                ->whereRaw('LEFT(jam_mulai, 5) < ?', [$bEnd])
                ->whereRaw('LEFT(jam_selesai, 5) > ?', [$bStart])
                ->exists();

            if ($bentrokKuliahRA) {
                throw new \Exception("Gagal menugaskan RA di jam {$bStart}-{$bEnd}. Bentrok dengan jam kuliah asisten.");
            }

            $durationMins = round((strtotime($bEnd) - strtotime($bStart)) / 60);
            $calculatedSks = max(1, round($durationMins / 50));

            foreach ($period as $date) {
                if (strtolower($date->locale('id')->translatedFormat('l')) === strtolower($day)) {
                    Schedule::create([
                        'tanggal' => $date->format('Y-m-d'),
                        'hari' => $day,
                        'id_lab' => $ruangRAObj->id_lab,
                        'id_asisten' => $asistenObj->id_asisten,
                        'jam_mulai' => $bStart,
                        'jam_selesai' => $bEnd,
                        'matkul' => 'RA',
                        'sks' => $calculatedSks,
                        'dosen' => '-',
                    ]);
                }
            }
        }
    }
}
