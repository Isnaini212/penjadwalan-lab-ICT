@extends('layouts.spv')

@section('title', 'Manajemen Jadwal Asisten')

@section('content')
<script defer src="{{ asset('js/spv-table.js') }}"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<div class="mb-10 rounded-2xl border border-slate-200 bg-white p-6 md:p-8 shadow-xl shadow-slate-200/40">
    
    {{-- Header Section --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
            <h3 class="text-xl font-extrabold text-slate-900 md:text-2xl">
                <i class="fas fa-calendar-alt mr-2 text-indigo-500"></i> Master Matrix Jadwal Asisten (Blueprint)
            </h3>
            <p class="mt-1 text-sm font-medium text-slate-500">
                Ubah kotak pilihan sesuka hati, lalu klik tombol simpan untuk memperbarui secara serentak.
            </p>
        </div>
    </div>

    {{-- Filter Selector Nama --}}
    <form method="GET" action="" class="mb-8 flex w-full flex-wrap items-end gap-4 rounded-xl border border-slate-200 bg-slate-50 p-5 md:w-max md:min-w-[400px]">
        <div class="w-full">
            <label class="mb-2 block text-xs font-extrabold uppercase tracking-wider text-slate-500">
                Pilih Nama Asisten Jaga:
            </label>
            <div class="relative">
                <select name="view_asisten" required onchange="this.form.submit()" class="w-full appearance-none rounded-xl border border-slate-300 bg-white px-4 py-3 pr-10 text-sm font-bold text-slate-800 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 cursor-pointer">
                    <option value="">-- Cari & Pilih Nama Asisten --</option>
                    @foreach($all_asisten as $asst)
                        <option value="{{ $asst }}" {{ request('view_asisten') == $asst ? 'selected' : '' }}>
                            {{ strtoupper($asst) }}
                        </option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-slate-400">
                    <i class="fas fa-chevron-down text-xs"></i>
                </div>
            </div>
        </div>
    </form>

    {{-- Filter Pengunci & Area Matrix --}}
    @if(request('view_asisten'))
        <form action="{{ route('schedule.matrix.update') }}" method="POST" id="form-matrix-massal">
            @csrf
            <input type="hidden" name="nama_asisten" value="{{ $selectedAsisten }}">

            {{-- Toolbar Atas Tabel --}}
            <div class="mb-4 flex justify-end">
                <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-emerald-500 px-6 py-2.5 text-sm font-bold text-white shadow-lg shadow-emerald-500/30 transition hover:bg-emerald-600 hover:-translate-y-0.5">
                    <i class="fas fa-save"></i> Simpan Blueprint
                </button>
            </div>

            {{-- Notifikasi --}}
            @if(session('success'))
                <div class="mb-5 rounded-xl bg-emerald-50 px-5 py-3.5 text-sm font-bold text-emerald-700 border border-emerald-200 flex items-center gap-3">
                    <i class="fas fa-check-circle text-emerald-500 text-lg"></i> {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="mb-5 rounded-xl bg-red-50 px-5 py-3.5 text-sm font-bold text-red-700 border border-red-200 flex items-center gap-3">
                    <i class="fas fa-exclamation-triangle text-red-500 text-lg"></i> {{ session('error') }}
                </div>
            @endif

            {{-- Tabel Matrix --}}
            <div class="overflow-x-auto rounded-xl border border-slate-300 shadow-sm custom-scrollbar">
                <table class="w-full min-w-[1000px] border-collapse text-center font-sans text-[12px]">
                    <thead>
                        <tr class="bg-slate-800 text-white">
                            <th class="border border-slate-700 bg-slate-900 p-3.5 font-extrabold w-[120px] tracking-wider uppercase text-xs">Waktu</th>
                            @foreach($dayNames as $day)
                                <th class="border border-slate-700 p-3.5 font-black tracking-wide uppercase w-[160px]">
                                    {{ $day }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($timeSlots as $slot)
                            <tr class="transition hover:bg-slate-50/50">
                                {{-- Kolom Jam --}}
                                <td class="border border-slate-200 bg-slate-50 p-2.5 font-extrabold text-slate-700 tracking-wider">
                                    {{ $slot['label'] }}
                                </td>

                                @foreach($dayNames as $day)
                                    @if(strtolower($day) === 'jumat' && ($slot['start'] === '11:35' || $slot['start'] === '12:30'))
                                        <td class="border border-slate-200 bg-blue-600 p-2.5 text-[11px] font-black uppercase text-white tracking-widest shadow-inner">
                                            SHOLAT JUMAT / BREAK
                                        </td>
                                    @else
                                        @php
                                            $dayLower = strtolower($day);
                                            $timeKey  = $slot['start'];
                                            $dropdownMatkul = $coursesBySlot[$dayLower][$timeKey] ?? [];

                                            // 1. Jadwal Kuliah Dia Sendiri
                                            $kelasKuliah = $weeklyClasses->first(function($item) use ($day, $slot) {
                                                if (strtolower($item->hari) !== strtolower($day)) return false;
                                                $itemStart = substr($item->jam_mulai, 0, 5);
                                                $itemEnd   = substr($item->jam_selesai, 0, 5);
                                                return ($itemStart < $slot['end'] && $itemEnd > $slot['start']);
                                            });

                                            // 2. Jadwal Dia Ditugaskan (Asisten / RA)
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
                                                $isStartHour = (substr($jadwalKalender->jam_mulai, 0, 5) === $slot['start']);
                                                $isTengahSesi = !$isStartHour;
                                                $cleanMatkul = trim(strtoupper($jadwalKalender->matkul));
                                                $statusAwal = ($cleanMatkul === 'RA') ? 'RA' : 'ASISTEN_LAB';
                                                $namaMatkulAktif = trim($jadwalKalender->matkul);
                                            } elseif ($kelasKuliah) {
                                                $isStartHour = (substr($kelasKuliah->jam_mulai, 0, 5) === $slot['start']);
                                                $isTengahSesi = !$isStartHour;
                                                $statusAwal = 'KULIAH_SENDIRI';
                                                $namaMatkulAktif = trim($kelasKuliah->mata_kuliah);
                                            }
                                        @endphp

                                        @if($isTengahSesi)
                                            {{-- TENGAH SESI TERKUNCI --}}
                                            @php
                                                $bgColor = ($statusAwal === 'RA') ? 'bg-yellow-200' : (($statusAwal === 'KULIAH_SENDIRI') ? 'bg-red-300' : 'bg-sky-500');
                                                $textColor = ($statusAwal === 'RA') ? 'text-yellow-800' : (($statusAwal === 'KULIAH_SENDIRI') ? 'text-red-900' : 'text-white');
                                                $prefixLabel = ($statusAwal === 'RA') ? 'RA' : '';
                                            @endphp
                                            <td class="border border-slate-200 p-2 text-[10px] font-bold opacity-80 cursor-not-allowed {{ $bgColor }} {{ $textColor }}">
                                                {{ $prefixLabel }}{{ $prefixLabel ? ': ' : '' }}{{ strtoupper(\Illuminate\Support\Str::limit($namaMatkulAktif, 12)) }}
                                            </td>
                                        @else
                                            {{-- KONDISI NORMAL: AWAL SESI / KOSONG --}}
                                            <input type="hidden" name="old_cells[{{ $day }}][{{ $slot['start'] }}]" value="{{ $statusAwal === 'ASISTEN_LAB' ? $namaMatkulAktif : $statusAwal }}">

                                            @if($statusAwal === 'KULIAH_SENDIRI')
                                                {{--  SIBUK KULIAH PRIBADI --}}
                                                <td class="border border-slate-200 bg-red-300 p-1">
                                                    <input type="hidden" name="cells[{{ $day }}][{{ $slot['start'] }}]" value="KULIAH_SENDIRI">
                                                    <select disabled class="w-full appearance-none bg-transparent text-center text-[11px] font-black text-red-900 outline-none cursor-not-allowed">
                                                        <option>{{ strtoupper(\Illuminate\Support\Str::limit($namaMatkulAktif, 12)) }}</option>
                                                    </select>
                                                </td>

                                            @elseif($statusAwal === 'RA')
                                                {{--  JAGA RA OFFICE --}}
                                                <td class="border border-slate-200 bg-yellow-200 p-1 transition-colors duration-300">
                                                    <select name="cells[{{ $day }}][{{ $slot['start'] }}]" onchange="gantiWarnaSilent(this)" class="w-full appearance-none bg-transparent text-center text-[12px] font-black text-yellow-800 outline-none cursor-pointer">
                                                        <option value="RA" selected> RA</option>
                                                        <option value="KOSONG" class="bg-white text-red-500 font-normal"> Kosongkan</option>
                                                        @foreach(array_unique($dropdownMatkul) as $m)
                                                            @php $mClean = trim($m); @endphp
                                                            @if(strtoupper($mClean) === 'RA' || strtoupper($mClean) === 'KOSONG') @continue @endif
                                                            <option value="{{ $mClean }}" class="bg-sky-500 text-white font-bold"> {{ strtoupper($mClean) }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>

                                            @elseif($statusAwal === 'ASISTEN_LAB')
                                                {{--  ASISTEN PRAKTIKUM --}}
                                                <td class="border border-slate-200 bg-sky-500 p-1 transition-colors duration-300">
                                                    <select name="cells[{{ $day }}][{{ $slot['start'] }}]" onchange="gantiWarnaSilent(this)" class="w-full appearance-none bg-transparent text-center text-[11px] font-black text-white outline-none cursor-pointer">
                                                        <option value="{{ $namaMatkulAktif }}" selected class="bg-white text-slate-800">
                                                             {{ strtoupper($namaMatkulAktif) }}
                                                        </option>
                                                        @foreach(array_unique($dropdownMatkul) as $m)
                                                            @php $mClean = trim($m); @endphp
                                                            @if(strtoupper($mClean) === 'RA' || strtoupper($mClean) === 'KOSONG' || strtolower($mClean) === strtolower($namaMatkulAktif)) 
                                                                @continue 
                                                            @endif
                                                            <option value="{{ $mClean }}" class="bg-sky-500 text-white font-bold">
                                                                 {{ strtoupper($mClean) }}
                                                            </option>
                                                        @endforeach
                                                        <option value="RA" class="bg-yellow-200 text-yellow-800 font-bold"> Jaga RA</option>
                                                        <option value="KOSONG" class="bg-white text-red-500 font-normal"> Lepas Tugas</option>
                                                    </select>
                                                </td>

                                            @else
                                                {{--  SLOT KOSONG --}}
                                                <td class="border border-slate-200 bg-white p-1 transition-colors duration-300">
                                                    <select name="cells[{{ $day }}][{{ $slot['start'] }}]" onchange="gantiWarnaSilent(this)" class="w-full appearance-none bg-transparent text-center text-[12px] font-bold text-slate-400 outline-none cursor-pointer hover:text-slate-600">
                                                        <option value="KOSONG" selected>---</option>
                                                        <option value="RA" class="bg-yellow-200 text-yellow-800 font-bold"> Jaga RA</option>
                                                        @foreach(array_unique($dropdownMatkul) as $m)
                                                            @php $mClean = trim($m); @endphp
                                                            @if(strtoupper($mClean) === 'RA' || strtoupper($mClean) === 'KOSONG') @continue @endif
                                                            <option value="{{ $mClean }}" class="bg-sky-500 text-white font-bold">🔬 {{ strtoupper($mClean) }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                            @endif
                                        @endif
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Toolbar Bawah Tabel --}}
            <div class="mt-6 flex justify-end">
                <button type="submit" id="btn-submit-matrix-final" class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-8 py-3.5 text-sm font-black text-white shadow-lg shadow-indigo-600/30 transition hover:bg-indigo-700 hover:-translate-y-0.5 focus:ring-4 focus:ring-indigo-600/20">
                    <i class="fas fa-rocket"></i> Eksekusi & Simpan Blueprint
                </button>
            </div>
        </form>
    @else
        {{-- State Kosong (Belum Pilih Asisten) --}}
        <div class="flex flex-col items-center justify-center rounded-2xl border-2 border-dashed border-slate-300 bg-slate-50 py-16 px-6 text-center">
            <div class="mb-4 flex h-16 w-16 items-center justify-center rounded-full bg-slate-200 text-slate-400">
                <i class="fas fa-lock text-2xl"></i>
            </div>
            <h4 class="mb-2 text-lg font-extrabold text-slate-700">Sistem Blueprint Terkunci</h4>
            <p class="text-sm font-medium text-slate-500 max-w-md">
                Silakan pilih Nama Asisten pada dropdown di atas terlebih dahulu untuk memunculkan tabel matriks template mingguan.
            </p>
        </div>
    @endif
</div>

<script>
// Fungsi Interaktif Warna Dropdown (Tanpa Reload)
function gantiWarnaSilent(selectElement) {
    const parentTd = selectElement.parentElement;
    const value = selectElement.value;

    // Hapus semua class warna background Tailwind bawaan TD
    parentTd.classList.remove('bg-white', 'bg-yellow-200', 'bg-sky-500');
    // Hapus semua class warna text Tailwind bawaan Select
    selectElement.classList.remove('text-slate-400', 'text-yellow-800', 'text-white', 'font-bold', 'font-black');

    if (value === 'KOSONG') {
        parentTd.classList.add('bg-white');
        selectElement.classList.add('text-slate-400', 'font-bold');
    } else if (value === 'RA') {
        parentTd.classList.add('bg-yellow-200');
        selectElement.classList.add('text-yellow-800', 'font-black');
    } else {
        // Otomatis jadi Biru (Tugas Asisten)
        parentTd.classList.add('bg-sky-500');
        selectElement.classList.add('text-white', 'font-black');
    }
}

// Efek Loading Saat Disubmit
document.getElementById('form-matrix-massal')?.addEventListener('submit', function() {
    const btn = document.getElementById('btn-submit-matrix-final');
    if(btn) {
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyinkronkan Jadwal...';
        btn.classList.add('opacity-70', 'pointer-events-none');
    }
});
</script>

<style>
/* Modifikasi scrollbar khusus untuk tabel biar lebih manis */
.custom-scrollbar::-webkit-scrollbar {
    height: 10px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 8px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 8px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
@endsection