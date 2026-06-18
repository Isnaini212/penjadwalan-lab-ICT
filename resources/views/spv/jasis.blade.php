@extends('layouts.spv')

@section('title', 'Matrix Jadwal Asisten')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900">Matrix Penugasan Asisten</h1>
        <p class="mt-1 text-sm font-medium text-slate-500">Pilih asisten dan tugaskan jadwal praktikum atau jaga RA.</p>
    </div>

    {{-- Notifikasi --}}
    @if(session('success'))
        <div class="rounded-xl bg-emerald-50 p-4 text-sm font-bold text-emerald-700 border border-emerald-200 flex items-center shadow-sm">
            <i class="fas fa-check-circle mr-3 text-emerald-500 text-lg"></i> {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="rounded-xl bg-red-50 p-4 text-sm font-bold text-red-700 border border-red-200 flex items-center shadow-sm">
            <i class="fas fa-exclamation-triangle mr-3 text-red-500 text-lg"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Filter Pilih Asisten --}}
    <div class="rounded-2xl border border-white/80 bg-white/80 p-5 shadow-xl shadow-blue-950/5 backdrop-blur">
        <form action="{{ route('spv.jasis') }}" method="GET" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[250px]">
                <label class="block text-xs font-bold uppercase tracking-wider text-slate-500 mb-2">Pilih Asisten</label>
                <select name="view_asisten" onchange="this.form.submit()" class="h-11 w-full rounded-xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-700 outline-none focus:border-blue-500 focus:ring-4 focus:ring-blue-500/10 cursor-pointer">
                    <option value="">-- Pilih Asisten untuk Ditugaskan --</option>
                    @foreach($all_asisten as $ast)
                        <option value="{{ $ast }}" {{ $selectedAsisten === $ast ? 'selected' : '' }}>{{ $ast }}</option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    {{-- MATRIKS JADWAL --}}
    @if($selectedAsisten)
        {{-- PASTIKAN route action di bawah ini mengarah ke fungsi updateMatrixRA lu --}}
        <form action="{{ route('schedule.matrix.update') }}" method="POST" class="rounded-2xl border border-slate-200 bg-white shadow-xl shadow-blue-950/5 overflow-hidden">
            @csrf
            <input type="hidden" name="nama_asisten" value="{{ $selectedAsisten }}">

            <div class="bg-blue-900 px-6 py-4 flex items-center justify-between">
                <h3 class="text-sm font-extrabold text-white uppercase tracking-wide">
                    <i class="fas fa-calendar-alt text-blue-300 mr-2"></i> Jadwal Matriks: {{ $selectedAsisten }}
                </h3>
                <button type="submit" class="inline-flex h-9 items-center gap-2 rounded-lg bg-emerald-500 px-4 text-xs font-black uppercase text-white shadow-md shadow-emerald-500/30 transition hover:bg-emerald-600 hover:-translate-y-0.5">
                    <i class="fas fa-save"></i> Simpan Matrix
                </button>
            </div>

            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full min-w-[900px] border-collapse text-center text-sm">
                    <thead class="bg-slate-50 text-slate-600 font-extrabold uppercase tracking-wider text-xs border-b border-slate-200">
                        <tr>
                            <th class="p-4 border-r border-slate-200 w-32">Slot Jam</th>
                            @foreach($dayNames as $day)
                                <th class="p-4 border-r border-slate-200 last:border-0">{{ $day }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($timeSlots as $slot)
                            <tr class="transition hover:bg-slate-50/50">
                                {{-- Kolom Jam --}}
                                <td class="border-r border-slate-200 bg-slate-50 p-2.5 font-bold text-slate-700 tracking-wider font-mono text-xs">
                                    {{ $slot['label'] }}
                                </td>

                                {{-- Kolom Hari-Hari --}}
                                @foreach($dayNames as $day)
                                    @php
    $dayLower = strtolower($day);
    $timeKey  = $slot['start'];
    $dropdownMatkul = $coursesBySlot[$dayLower][$timeKey] ?? [];

    // 1. Cari Jadwal Kuliah Dia Sendiri
    $kelasKuliah = $weeklyClasses->first(function($item) use ($day, $slot) {
        if (strtolower($item->hari) !== strtolower($day)) return false;
        $itemStart = substr($item->jam_mulai, 0, 5);
        $itemEnd   = substr($item->jam_selesai, 0, 5);
        return ($itemStart < $slot['end'] && $itemEnd > $slot['start']);
    });

    // 2. Cari Jadwal Tugas Asisten (Praktikum Lab / RA)
    $jadwalKalender = $assistantAllSchedules->first(function($item) use ($day, $slot) {
        if (strtolower($item->hari) !== strtolower($day)) return false;
        $itemStart = substr($item->jam_mulai, 0, 5);
        $itemEnd   = substr($item->jam_selesai, 0, 5);
        return ($itemStart < $slot['end'] && $itemEnd > $slot['start']);
    });

    $statusAwal = 'KOSONG';
    $namaMatkulAktif = '';
    $isStartHour = false;
    $isTengahSesi = false;

    if ($jadwalKalender) {
        $itemStart = substr($jadwalKalender->jam_mulai, 0, 5);
        $itemEnd   = substr($jadwalKalender->jam_selesai, 0, 5);

        // 🌟 LOGIKA BARU: Cari kotak slot pertama yang bersentuhan dengan jam matkul ini
        $firstSlot = collect($timeSlots)->first(function($ts) use ($itemStart, $itemEnd) {
            return ($itemStart < $ts['end'] && $itemEnd > $ts['start']);
        });

        // Jika kotak saat ini adalah kotak pertama yang bersentuhan, munculkan Dropdown!
        $isStartHour = $firstSlot && ($firstSlot['start'] === $slot['start']);
        $isTengahSesi = !$isStartHour;

        $cleanMatkul = trim(strtoupper($jadwalKalender->matkul));
        $statusAwal = ($cleanMatkul === 'RA') ? 'RA' : 'ASISTEN_LAB';
        $namaMatkulAktif = trim($jadwalKalender->matkul);

    } elseif ($kelasKuliah) {
        $itemStart = substr($kelasKuliah->jam_mulai, 0, 5);
        $itemEnd   = substr($kelasKuliah->jam_selesai, 0, 5);

        // 🌟 LOGIKA BARU BERLAKU SAMA UNTUK KULIAH PRIBADI
        $firstSlot = collect($timeSlots)->first(function($ts) use ($itemStart, $itemEnd) {
            return ($itemStart < $ts['end'] && $itemEnd > $ts['start']);
        });

        $isStartHour = $firstSlot && ($firstSlot['start'] === $slot['start']);
        $isTengahSesi = !$isStartHour;

        $statusAwal = 'KULIAH_SENDIRI';
        $namaMatkulAktif = trim($kelasKuliah->mata_kuliah);
    }
@endphp

                                    @if($isTengahSesi)
                                        {{-- 🔒 TENGAH SESI TERKUNCI (Ekor dari Sesi Sebelumnya) --}}
                                        @php
                                            $bgColor = ($statusAwal === 'RA') ? 'bg-yellow-200' : (($statusAwal === 'KULIAH_SENDIRI') ? 'bg-slate-200' : 'bg-sky-500');
                                            $textColor = ($statusAwal === 'RA') ? 'text-yellow-800' : (($statusAwal === 'KULIAH_SENDIRI') ? 'text-slate-800' : 'text-white');
                                            $prefixLabel = ($statusAwal === 'RA') ? 'RA' : '';
                                        @endphp
                                        <td class="border-r border-slate-200 p-2 text-[10px] font-bold opacity-80 cursor-not-allowed {{ $bgColor }} {{ $textColor }}">
                                            {{-- Hidden input agar rantai data merge di database gak putus --}}
                                            <input type="hidden" name="old_cells[{{ $day }}][{{ $slot['start'] }}]" value="{{ $statusAwal === 'ASISTEN_LAB' ? $namaMatkulAktif : $statusAwal }}">
                                            <input type="hidden" name="cells[{{ $day }}][{{ $slot['start'] }}]" value="{{ $statusAwal === 'ASISTEN_LAB' ? $namaMatkulAktif : $statusAwal }}">

                                            {{ $prefixLabel }}{{ $prefixLabel ? ': ' : '' }}{{ strtoupper(\Illuminate\Support\Str::limit($namaMatkulAktif, 12)) }}
                                        </td>
                                    @else
                                        {{-- 🟢 KONDISI NORMAL: AWAL SESI / KOSONG --}}
                                        <input type="hidden" name="old_cells[{{ $day }}][{{ $slot['start'] }}]" value="{{ $statusAwal === 'ASISTEN_LAB' ? $namaMatkulAktif : $statusAwal }}">

                                        @if($statusAwal === 'KULIAH_SENDIRI')
                                            {{-- SIBUK KULIAH PRIBADI --}}
                                            <td class="border-r border-slate-200 bg-slate-200 p-1">
                                                <input type="hidden" name="cells[{{ $day }}][{{ $slot['start'] }}]" value="KULIAH_SENDIRI">
                                                <select disabled class="w-full appearance-none bg-transparent text-center text-[11px] font-black text-slate-700 outline-none cursor-not-allowed">
                                                    <option>{{ strtoupper(\Illuminate\Support\Str::limit($namaMatkulAktif, 12)) }}</option>
                                                </select>
                                            </td>

                                        @elseif($statusAwal === 'RA')
                                            {{-- JAGA RUANG ASISTEN --}}
                                            <td class="border-r border-slate-200 bg-yellow-200 p-1 transition-colors duration-300">
                                                <select name="cells[{{ $day }}][{{ $slot['start'] }}]" onchange="gantiWarnaSilent(this)" class="w-full appearance-none bg-transparent text-center text-[12px] font-black text-yellow-800 outline-none cursor-pointer">
                                                    <option value="RA" selected>📌 RA</option>
                                                    <option value="KOSONG" class="bg-white text-red-500 font-normal">⚪ Kosongkan</option>
                                                    @foreach(array_unique($dropdownMatkul) as $m)
                                                        @php $mClean = trim($m); @endphp
                                                        @if(strtoupper($mClean) === 'RA' || strtoupper($mClean) === 'KOSONG') @continue @endif
                                                        <option value="{{ $mClean }}" class="bg-sky-500 text-white font-bold">🔬 {{ strtoupper($mClean) }}</option>
                                                    @endforeach
                                                </select>
                                            </td>

                                        @elseif($statusAwal === 'ASISTEN_LAB')
                                            {{-- NGAJAR MATKUL LAB --}}
                                            <td class="border-r border-slate-200 bg-sky-500 p-1 transition-colors duration-300">
                                                <select name="cells[{{ $day }}][{{ $slot['start'] }}]" onchange="gantiWarnaSilent(this)" class="w-full appearance-none bg-transparent text-center text-[11px] font-black text-white outline-none cursor-pointer">
                                                    <option value="{{ $namaMatkulAktif }}" selected class="bg-white text-slate-800">
                                                        🔬 {{ strtoupper($namaMatkulAktif) }}
                                                    </option>
                                                    @foreach(array_unique($dropdownMatkul) as $m)
                                                        @php $mClean = trim($m); @endphp
                                                        @if(strtoupper($mClean) === 'RA' || strtoupper($mClean) === 'KOSONG' || strtolower($mClean) === strtolower($namaMatkulAktif))
                                                            @continue
                                                        @endif
                                                        <option value="{{ $mClean }}" class="bg-sky-500 text-white font-bold">
                                                            🔬 {{ strtoupper($mClean) }}
                                                        </option>
                                                    @endforeach
                                                    <option value="RA" class="bg-yellow-200 text-yellow-800 font-bold">📌 Jaga RA</option>
                                                    <option value="KOSONG" class="bg-white text-red-500 font-normal">⚪ Lepas Tugas</option>
                                                </select>
                                            </td>

                                        @else
                                            {{-- SLOT MASIH KOSONG --}}
                                            <td class="border-r border-slate-200 bg-white p-1 transition-colors duration-300">
                                                <select name="cells[{{ $day }}][{{ $slot['start'] }}]" onchange="gantiWarnaSilent(this)" class="w-full appearance-none bg-transparent text-center text-[12px] font-bold text-slate-400 outline-none cursor-pointer hover:text-slate-600">
                                                    <option value="KOSONG" selected>---</option>
                                                    <option value="RA" class="bg-yellow-200 text-yellow-800 font-bold">📌 Jaga RA</option>
                                                    @foreach(array_unique($dropdownMatkul) as $m)
                                                        @php $mClean = trim($m); @endphp
                                                        @if(strtoupper($mClean) === 'RA' || strtoupper($mClean) === 'KOSONG') @continue @endif
                                                        <option value="{{ $mClean }}" class="bg-sky-500 text-white font-bold">🔬 {{ strtoupper($mClean) }}</option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        @endif
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </form>
    @else
        {{-- EMPTY STATE: Belum Pilih Asisten --}}
        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300 bg-white p-12 text-center text-slate-400">
            <div class="h-20 w-20 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-user-clock text-4xl text-slate-300"></i>
            </div>
            <h3 class="font-bold text-lg text-slate-600">Pilih Asisten Terlebih Dahulu</h3>
            <p class="text-sm font-medium mt-1 max-w-md">Silakan pilih nama asisten dari dropdown di atas untuk mulai mengatur dan menyinkronkan jadwal matriksnya.</p>
        </div>
    @endif
</div>

{{-- SCRIPT JAVASCRIPT UNTUK GANTI WARNA FORM SECARA OTOMATIS --}}
<script>
    function gantiWarnaSilent(selectElement) {
        const td = selectElement.parentElement;
        const value = selectElement.value;

        // Reset class sebelum ditimpa
        td.classList.remove('bg-white', 'bg-yellow-200', 'bg-sky-500', 'text-slate-400', 'text-yellow-800', 'text-white');
        selectElement.classList.remove('text-slate-400', 'text-yellow-800', 'text-white');

        if (value === 'RA') {
            td.classList.add('bg-yellow-200');
            selectElement.classList.add('text-yellow-800');
        } else if (value === 'KOSONG') {
            td.classList.add('bg-white');
            selectElement.classList.add('text-slate-400');
        } else {
            // Jika pilih mata kuliah Lab
            td.classList.add('bg-sky-500');
            selectElement.classList.add('text-white');
        }
    }
</script>
@endsection
