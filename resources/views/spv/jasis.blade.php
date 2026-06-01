<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="{{ asset('js/spv-table.js') }}"></script>
    <link class="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <title>Manajemen Jadwal</title>
</head>
<body>
@extends('layouts.spv')

@section('title', 'Manajemen Jadwal Asisten')

@section('content')
<div style="background: white; border-radius: 16px; padding: 25px; box-shadow: 0 10px 15px -3px rgba(0,0,0,0.05); border: 1px solid #e2e8f0; margin-bottom: 35px;">
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
        <div>
            <h3 style="margin: 0; color: #1e293b; font-size: 18px; font-weight: 700;"> Bars Master Matrix Jadwal Mingguan Asisten (Semester Blueprint)</h3>
            <p style="color: #64748b; font-size: 13px; margin: 2px 0 0 0;">Ubah beberapa kotak pilihan sesuka hati terlebih dahulu, kemudian klik tombol simpan untuk memperbarui secara serentak.</p>
        </div>
    </div>

    {{-- Filter Selector Nama --}}
    <form method="GET" action="" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; background: #f8fafc; padding: 15px; border-radius: 10px; border: 1px solid #e2e8f0; margin-bottom: 25px; width: max-content; min-width: 350px;">
        <div style="flex: 1;">
            <label style="display: block; font-size: 12px; font-weight: 700; color: #475569; margin-bottom: 6px;">Pilih Nama Asisten Jaga:</label>
            <select name="view_asisten" required onchange="this.form.submit()" style="width: 100%; padding: 10px; border-radius: 8px; border: 1px solid #cbd5e1; background: white; font-weight: 600; color: #0f172a; cursor: pointer;">
                <option value="">-- Cari & Pilih Nama Asisten --</option>
                @foreach($all_asisten as $asst)
                    <option value="{{ $asst }}" {{ request('view_asisten') == $asst ? 'selected' : '' }}>{{ strtoupper($asst) }}</option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- Filter Pengunci --}}
    @if(request('view_asisten'))
        <form action="{{ route('schedule.matrix.update') }}" method="POST" id="form-matrix-massal">
            @csrf
            <input type="hidden" name="nama_asisten" value="{{ $selectedAsisten }}">

            {{-- Toolbar Atas Tabel --}}
            <div style="text-align: right; margin-bottom: 15px;">
                <button type="submit" style="padding: 10px 20px; background: #10b981; color: white; border: none; border-radius: 8px; font-weight: 700; font-size: 13px; cursor: pointer; box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.2);">
                    💾 Simpan Semua Perubahan Blueprint
                </button>
            </div>

            <div style="overflow-x: auto; border: 1px solid #cbd5e1; border-radius: 12px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.02);">
                <table style="width: 100%; border-collapse: collapse; text-align: center; font-family: 'Inter', sans-serif; font-size: 12.5px; min-width: 900px;">
                    <thead>
                        <tr style="background: #1e293b; color: white;">
                            <th style="padding: 14px; font-weight: 800; border: 1px solid #334155; width: 130px; background: #0f172a;">WAKTU</th>
                            @foreach($dayNames as $day)
                                <th style="padding: 14px; border: 1px solid #334155; font-weight: 900; letter-spacing: 0.5px; text-transform: uppercase; width: 160px;">
                                    {{ $day }}
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    @if(session('success'))
                        <div style="background: #10b981; color: white; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div style="background: #ef4444; color: white; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; font-weight: bold;">
                            ❌ {{ session('error') }}
                        </div>
                    @endif

                    <tbody>
                        @foreach($timeSlots as $slot)
                            <tr>
                                <td style="padding: 11px; font-weight: 700; background: #f8fafc; border: 1px solid #cbd5e1; color: #334155;">
                                    {{ $slot['label'] }}
                                </td>

                                @foreach($dayNames as $day)
                                    @if(strtolower($day) === 'jumat' && ($slot['start'] === '11:35' || $slot['start'] === '12:30'))
                                        <td style="background: #2563eb; color: white; font-weight: 800; font-size: 12px; padding: 10px; border: 1px solid #cbd5e1; text-transform: uppercase;">
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

                                            // 2. Jadwal Dia Ditugaskan (Sebagai Asisten Praktikum / RA)
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
                                                $statusAwal = ($jadwalKalender->lab === 'RUANG RA') ? 'RA' : 'ASISTEN_LAB';
                                                $namaMatkulAktif = $jadwalKalender->matkul;
                                            } elseif ($kelasKuliah) {
                                                $isStartHour = (substr($kelasKuliah->jam_mulai, 0, 5) === $slot['start']);
                                                $isTengahSesi = !$isStartHour;
                                                $statusAwal = 'KULIAH_SENDIRI';
                                                $namaMatkulAktif = $kelasKuliah->mata_kuliah;
                                            }
                                        @endphp

                                        @if($isTengahSesi)
                                            {{-- TENGAH SESI TERKUNCI --}}
                                            @php
                                                $bgColor = ($statusAwal === 'RA') ? '#fef08a' : (($statusAwal === 'KULIAH_SENDIRI') ? '#fca5a5' : '#0ea5e9');
                                                $textColor = ($statusAwal === 'RA') ? '#854d0e' : (($statusAwal === 'KULIAH_SENDIRI') ? '#7f1d1d' : '#ffffff');
                                                $prefixLabel = ($statusAwal === 'RA') ? 'RA' : '';
                                            @endphp
                                            <td style="padding: 8px; border: 1px solid #cbd5e1; background-color: {{ $bgColor }}; color: {{ $textColor }}; font-weight: 700; font-size: 11px; cursor: not-allowed; opacity: 0.85;">
                                                {{ $prefixLabel }}{{ $prefixLabel ? ': ' : '' }}{{ strtoupper(\Illuminate\Support\Str::limit($namaMatkulAktif, 12)) }}
                                            </td>
                                        @else
                                            {{-- KONDISI NORMAL: AWAL SESI / KOSONG --}}
                                            <input type="hidden" name="old_cells[{{ $day }}][{{ $slot['start'] }}]" value="{{ $statusAwal === 'ASISTEN_LAB' ? $namaMatkulAktif : $statusAwal }}">

                                            @if($statusAwal === 'KULIAH_SENDIRI')
                                                {{-- ================= ⛔ SIBUK KULIAH PRIBADI (MERAH TERKUNCI) ================= --}}
                                                <td style="padding: 4px; border: 1px solid #cbd5e1; background-color: #fca5a5;">
                                                    <input type="hidden" name="cells[{{ $day }}][{{ $slot['start'] }}]" value="KULIAH_SENDIRI">
                                                    <select disabled style="background: transparent; border: none; font-weight: 800; color: #7f1d1d; text-align-last: center; width: 100%; font-size: 11.5px; cursor: not-allowed;">
                                                        <option>{{ strtoupper(\Illuminate\Support\Str::limit($namaMatkulAktif, 12)) }}</option>
                                                    </select>
                                                </td>

                                            @elseif($statusAwal === 'RA')
                                                {{-- ================= ⚠️ JAGA RA OFFICE (KUNING) ================= --}}
                                                <td style="padding: 4px; border: 1px solid #cbd5e1; background-color: #fef08a;">
                                                    <select name="cells[{{ $day }}][{{ $slot['start'] }}]" onchange="gantiWarnaSilent(this)" style="background: transparent; border: none; font-weight: 900; color: #854d0e; text-align-last: center; cursor: pointer; width: 100%; font-size: 12.5px;">
                                                        <option value="RA" selected>RA</option>
                                                        <option value="KOSONG" style="background: white; color: red; font-weight: normal;">⚪ Kosongkan</option>
                                                        {{-- 🔥 FIX SUNTIKAN 1: Filter Duplikat --}}
                                                        @foreach(array_unique($dropdownMatkul) as $m)
                                                            @if(strtoupper($m) === 'RA' || strtoupper($m) === 'KOSONG') @continue @endif
                                                            <option value="{{ $m }}" style="background: #0ea5e9; color: #ffffff;">🔬 {{ strtoupper($m) }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>

                                            @elseif($statusAwal === 'ASISTEN_LAB')
                                                {{-- ================= 🔬 ASISTEN PRAKTIKUM (BIRU CYAN) ================= --}}
                                                <td style="padding: 4px; border: 1px solid #cbd5e1; background-color: #0ea5e9;">
                                                    <select name="cells[{{ $day }}][{{ $slot['start'] }}]" onchange="gantiWarnaSilent(this)" style="background: transparent; border: none; font-weight: 900; color: #ffffff; text-align-last: center; cursor: pointer; width: 100%; font-size: 11.5px;">
                                                        {{-- 🔥 FIX SUNTIKAN 2: Filter Duplikat --}}
                                                        @foreach(array_unique($dropdownMatkul) as $m)
                                                            @if(strtoupper($m) === 'RA' || strtoupper($m) === 'KOSONG') @continue @endif
                                                            <option value="{{ $m }}" {{ strtolower($namaMatkulAktif) === strtolower($m) ? 'selected' : '' }} style="background: white; color: #334155;">
                                                                🔬 {{ strtoupper($m) }}
                                                            </option>
                                                        @endforeach
                                                        <option value="RA" style="background: #fef08a; color: #854d0e; font-weight: bold;">📌 Jaga RA</option>
                                                        <option value="KOSONG" style="background: white; color: red; font-weight: normal;">⚪ Lepas Tugas</option>
                                                    </select>
                                                </td>

                                            @else

                                                <td style="padding: 4px; border: 1px solid #cbd5e1; background-color: #ffffff;">
                                                    <select name="cells[{{ $day }}][{{ $slot['start'] }}]" onchange="gantiWarnaSilent(this)" style="background: transparent; border: none; color: #cbd5e1; text-align-last: center; cursor: pointer; width: 100%; font-size: 12.5px;">
                                                        <option value="KOSONG" selected>---</option>
                                                        <option value="RA" style="background: #fef08a; color: #854d0e; font-weight: bold;">📌 Jaga RA</option>
                                                       
                                                        @foreach(array_unique($dropdownMatkul) as $m)
                                                            @if(strtoupper($m) === 'RA' || strtoupper($m) === 'KOSONG') @continue @endif
                                                            <option value="{{ $m }}" style="background: #0ea5e9; color: #ffffff;">🔬 {{ strtoupper($m) }}</option>
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

            <div style="text-align: right; margin-top: 20px;">
                <button type="submit" id="btn-submit-matrix-final" style="padding: 14px 30px; background: #4f46e5; color: white; border: none; border-radius: 10px; font-weight: 700; font-size: 14px; cursor: pointer; box-shadow: 0 10px 15px -3px rgba(79, 70, 229, 0.3);">
                    Eksekusi & Simpan Blueprint Mingguan
                </button>
            </div>
        </form>
    @else
        <div style="text-align: center; color: #94a3b8; padding: 45px 20px; background: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 10px; font-size: 14px;">
            <b>Sistem Blueprint Terkunci:</b> Silakan tentukan Nama Asisten pada pilihan di atas untuk memunculkan tabel matriks template mingguan.
        </div>
    @endif
</div>

<script>
function gantiWarnaSilent(selectElement) {
    const parentTd = selectElement.parentElement;
    const value = selectElement.value;

    if (value === 'KOSONG') {
        parentTd.style.backgroundColor = '#ffffff';
        selectElement.style.color = '#cbd5e1';
        selectElement.style.fontWeight = 'normal';
    } else if (value === 'RA') {
        parentTd.style.backgroundColor = '#fef08a';
        selectElement.style.color = '#854d0e';
        selectElement.style.fontWeight = '900';
    } else {
        // Otomatis jadi Biru (Tugas Asisten)
        parentTd.style.backgroundColor = '#0ea5e9';
        selectElement.style.color = '#ffffff';
        selectElement.style.fontWeight = '900';
    }
}

document.getElementById('form-matrix-massal')?.addEventListener('submit', function() {
    const btn = document.getElementById('btn-submit-matrix-final');
    if(btn) {
        btn.innerHTML = '⏳ Sedang Menyinkronkan Jadwal...';
        btn.style.opacity = '0.7';
        btn.style.pointerEvents = 'none';
    }
});
</script>
@endsection
</body>
</html>